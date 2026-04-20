/*
  ESP32 + G1/2 Water Flow Sensor (YF-S201 style)
  Direct Wi-Fi HTTP posting to Laravel API.

  API endpoint expected:
  POST http://aquwatch.test/api/ingest/flow
  Headers:
    Content-Type: application/json
    X-Sensor-Token: <SENSOR_INGEST_TOKEN>
  Body:
    {
      "sensor_id": "flow-esp32-01",
      "flow_lpm": 1.23,
      "total_ml": 4567,
      "measured_at": "2026-04-19T07:50:11Z"
    }
*/

#include <WiFi.h>
#include <HTTPClient.h>
#include <time.h>

#define LED_BUILTIN 2
#define SENSOR_PIN 27

// Wi-Fi credentials
const char* WIFI_SSID = "Sanspenyu";
const char* WIFI_PASSWORD = "ayam1234";

// Laravel API settings
const char* API_URL = "http://172.17.42.94:8082/api/ingest/flow";
const char* SENSOR_TOKEN = "aqw_1f8d7a9b3c4e6f2a91d0b7e5c3a8f6d4b2c9e1a7f3d5b8c0";
const char* SENSOR_ID = "flow-esp32-01";

const uint32_t SAMPLE_INTERVAL_MS = 1000;
const uint32_t WIFI_RETRY_MS = 10000;
const float CALIBRATION_FACTOR = 4.5f; // pulses-per-second / 4.5 = L/min

volatile uint32_t pulseCount = 0;

uint32_t lastSampleMs = 0;
uint32_t lastWifiRetryMs = 0;
float flowRateLMin = 0.0f;
uint32_t flowMilliLitres = 0;
uint64_t totalMilliLitres = 0;
wl_status_t lastWifiStatus = WL_IDLE_STATUS;

const char* wifiStatusText(wl_status_t status) {
  switch (status) {
    case WL_IDLE_STATUS: return "IDLE";
    case WL_NO_SSID_AVAIL: return "NO_SSID_AVAIL";
    case WL_SCAN_COMPLETED: return "SCAN_COMPLETED";
    case WL_CONNECTED: return "CONNECTED";
    case WL_CONNECT_FAILED: return "CONNECT_FAILED";
    case WL_CONNECTION_LOST: return "CONNECTION_LOST";
    case WL_DISCONNECTED: return "DISCONNECTED";
    default: return "UNKNOWN";
  }
}

void IRAM_ATTR pulseCounter() {
  pulseCount++;
}

void connectWifiIfNeeded() {
  wl_status_t status = WiFi.status();

  if (status != lastWifiStatus) {
    Serial.print("[WiFi] Status changed: ");
    Serial.println(wifiStatusText(status));
    lastWifiStatus = status;

    if (status == WL_CONNECTED) {
      Serial.print("[WiFi] Connected. IP: ");
      Serial.println(WiFi.localIP());
      Serial.print("[WiFi] RSSI: ");
      Serial.println(WiFi.RSSI());
    }
  }

  if (status == WL_CONNECTED) {
    return;
  }

  uint32_t now = millis();
  if (now - lastWifiRetryMs < WIFI_RETRY_MS) {
    return;
  }

  lastWifiRetryMs = now;
  Serial.print("[WiFi] Connecting to ");
  Serial.print(WIFI_SSID);
  Serial.print(" (state: ");
  Serial.print(wifiStatusText(status));
  Serial.println(")...");
  WiFi.disconnect(true, false);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
}

String iso8601UtcNow() {
  struct tm timeinfo;
  if (!getLocalTime(&timeinfo, 200)) {
    // Fallback: if NTP is not available, send an empty timestamp and let backend set it.
    return "";
  }

  char buffer[25];
  strftime(buffer, sizeof(buffer), "%Y-%m-%dT%H:%M:%SZ", &timeinfo);
  return String(buffer);
}

void postReading(float flowLpm, uint64_t totalMl) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("[HTTP] Skipped: Wi-Fi not connected.");
    return;
  }

  HTTPClient http;
  http.setConnectTimeout(6000);
  http.setTimeout(6000);
  http.begin(API_URL);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");
  http.addHeader("X-Sensor-Token", SENSOR_TOKEN);

  String measuredAt = iso8601UtcNow();
  String body = "{";
  body += "\"sensor_id\":\"" + String(SENSOR_ID) + "\",";
  body += "\"flow_lpm\":" + String(flowLpm, 3) + ",";
  body += "\"total_ml\":" + String((unsigned long)totalMl);

  if (measuredAt.length() > 0) {
    body += ",\"measured_at\":\"" + measuredAt + "\"";
  }

  body += "}";

  int statusCode = http.POST(body);
  String response = http.getString();

  Serial.print("[HTTP] Status: ");
  Serial.print(statusCode);
  if (statusCode < 0) {
    Serial.print(" (");
    Serial.print(http.errorToString(statusCode));
    Serial.print(")");
  }
  Serial.print(" Response: ");
  Serial.println(response);

  http.end();
}

void setup() {
  Serial.begin(115200);
  delay(300);
  Serial.println("\n[Boot] ESP32 flow sender starting...");
  Serial.print("[Boot] API URL: ");
  Serial.println(API_URL);

  pinMode(LED_BUILTIN, OUTPUT);
  pinMode(SENSOR_PIN, INPUT_PULLUP);

  pulseCount = 0;
  lastSampleMs = millis();

  attachInterrupt(digitalPinToInterrupt(SENSOR_PIN), pulseCounter, FALLING);

  WiFi.mode(WIFI_STA);
  WiFi.setAutoReconnect(true);
  WiFi.setSleep(false);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  Serial.println("[WiFi] Initial connect request sent.");

  // Optional NTP sync for measured_at timestamps.
  configTime(0, 0, "pool.ntp.org", "time.nist.gov");
}

void loop() {
  connectWifiIfNeeded();

  uint32_t now = millis();

  if (now - lastSampleMs >= SAMPLE_INTERVAL_MS) {
    uint32_t elapsedMs = now - lastSampleMs;
    lastSampleMs = now;

    noInterrupts();
    uint32_t pulses = pulseCount;
    pulseCount = 0;
    interrupts();

    float frequency = (pulses * 1000.0f) / elapsedMs;
    flowRateLMin = frequency / CALIBRATION_FACTOR;

    float milliLitresThisInterval = (flowRateLMin / 60.0f) * 1000.0f * (elapsedMs / 1000.0f);
    flowMilliLitres = (uint32_t)(milliLitresThisInterval + 0.5f);
    totalMilliLitres += flowMilliLitres;

    digitalWrite(LED_BUILTIN, pulses > 0 ? HIGH : LOW);

    Serial.print("Flow rate: ");
    Serial.print(flowRateLMin, 2);
    Serial.print(" L/min\tOutput Liquid Quantity: ");
    Serial.print(totalMilliLitres);
    Serial.print(" mL / ");
    Serial.print(totalMilliLitres / 1000.0f, 3);
    Serial.println(" L");

    postReading(flowRateLMin, totalMilliLitres);
  }
}
