/*
  ESP32 + 2x G1/2 Water Flow Sensors (YF-S201 style)
  Direct Wi-Fi HTTP posting to Laravel API.

  Wiring for 2 sensors:
  - Both sensor VCC wires can be split to the same 5V rail.
  - Both sensor GND wires must go to ESP32 GND (common ground).
  - Each sensor signal must use a separate GPIO pin.

  API endpoint expected:
  POST https://aquwatch.org/api/ingest/flow
*/

#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <time.h>

#define LED_BUILTIN 2
#define SENSOR_A_PIN 27
#define SENSOR_B_PIN 26

// Wi-Fi credentials
const char* WIFI_SSID = "Honor";
const char* WIFI_PASSWORD = "indabulih";

// Laravel API settings
const char* API_URL = "https://aquwatch.org/api/ingest/flow";
const char* SENSOR_TOKEN = "aqw_1f8d7a9b3c4e6f2a91d0b7e5c3a8f6d4b2c9e1a7f3d5b8c0";
const char* SENSOR_A_ID = "flow-esp32-p27";
const char* SENSOR_B_ID = "flow-esp32-p26";

const uint32_t SAMPLE_INTERVAL_MS = 1000;
const uint32_t WIFI_RETRY_MS = 12000;
const uint32_t WIFI_CONNECT_GRACE_MS = 8000;
const float CALIBRATION_FACTOR = 4.5f; // pulses-per-second / 4.5 = L/min

volatile uint32_t pulseCountA = 0;
volatile uint32_t pulseCountB = 0;

uint32_t lastSampleMs = 0;
uint32_t lastWifiRetryMs = 0;
uint32_t wifiConnectStartedMs = 0;
float flowRateA_LMin = 0.0f;
float flowRateB_LMin = 0.0f;
float combinedFlowRateLMin = 0.0f;
uint32_t flowA_MilliLitres = 0;
uint32_t flowB_MilliLitres = 0;
uint64_t totalA_MilliLitres = 0;
uint64_t totalB_MilliLitres = 0;
uint64_t totalCombinedMilliLitres = 0;
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

void IRAM_ATTR pulseCounterA() {
  pulseCountA++;
}

void IRAM_ATTR pulseCounterB() {
  pulseCountB++;
}

void connectWifiIfNeeded() {
  wl_status_t status = WiFi.status();
  uint32_t now = millis();

  if (status != lastWifiStatus) {
    Serial.print("[WiFi] Status changed: ");
    Serial.println(wifiStatusText(status));
    lastWifiStatus = status;

    if (status == WL_CONNECTED) {
      Serial.print("[WiFi] Connected. IP: ");
      Serial.println(WiFi.localIP());
      Serial.print("[WiFi] RSSI: ");
      Serial.println(WiFi.RSSI());
      wifiConnectStartedMs = 0;
    }
  }

  if (status == WL_CONNECTED) {
    return;
  }

  if (wifiConnectStartedMs > 0 && (now - wifiConnectStartedMs) < WIFI_CONNECT_GRACE_MS) {
    return;
  }

  if (now - lastWifiRetryMs < WIFI_RETRY_MS) {
    return;
  }

  lastWifiRetryMs = now;
  wifiConnectStartedMs = now;

  Serial.print("[WiFi] Connecting to ");
  Serial.print(WIFI_SSID);
  Serial.print(" (state: ");
  Serial.print(wifiStatusText(status));
  Serial.println(")...");

  // Avoid force-disconnect loops; try reconnect first, then begin if needed.
  if (!WiFi.reconnect()) {
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  }
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

void postReading(const char* sensorId, float flowLpm, uint64_t totalMl) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("[HTTP] Skipped: Wi-Fi not connected.");
    return;
  }

  WiFiClientSecure client;
  client.setInsecure();

  HTTPClient http;
  http.setConnectTimeout(6000);
  http.setTimeout(6000);
  http.begin(client, API_URL);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");
  http.addHeader("X-Sensor-Token", SENSOR_TOKEN);

  String measuredAt = iso8601UtcNow();
  String body = "{";
  body += "\"sensor_id\":\"" + String(sensorId) + "\",";
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
  Serial.println("\n[Boot] ESP32 dual-flow sender starting...");
  Serial.print("[Boot] API URL: ");
  Serial.println(API_URL);

  pinMode(LED_BUILTIN, OUTPUT);
  pinMode(SENSOR_A_PIN, INPUT_PULLUP);
  pinMode(SENSOR_B_PIN, INPUT_PULLUP);

  pulseCountA = 0;
  pulseCountB = 0;
  lastSampleMs = millis();

  attachInterrupt(digitalPinToInterrupt(SENSOR_A_PIN), pulseCounterA, FALLING);
  attachInterrupt(digitalPinToInterrupt(SENSOR_B_PIN), pulseCounterB, FALLING);

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
    uint32_t pulsesA = pulseCountA;
    uint32_t pulsesB = pulseCountB;
    pulseCountA = 0;
    pulseCountB = 0;
    interrupts();

    float frequencyA = (pulsesA * 1000.0f) / elapsedMs;
    float frequencyB = (pulsesB * 1000.0f) / elapsedMs;
    flowRateA_LMin = frequencyA / CALIBRATION_FACTOR;
    flowRateB_LMin = frequencyB / CALIBRATION_FACTOR;
    combinedFlowRateLMin = flowRateA_LMin + flowRateB_LMin;

    float milliLitresA = (flowRateA_LMin / 60.0f) * 1000.0f * (elapsedMs / 1000.0f);
    float milliLitresB = (flowRateB_LMin / 60.0f) * 1000.0f * (elapsedMs / 1000.0f);
    flowA_MilliLitres = (uint32_t)(milliLitresA + 0.5f);
    flowB_MilliLitres = (uint32_t)(milliLitresB + 0.5f);
    totalA_MilliLitres += flowA_MilliLitres;
    totalB_MilliLitres += flowB_MilliLitres;
    totalCombinedMilliLitres = totalA_MilliLitres + totalB_MilliLitres;

    digitalWrite(LED_BUILTIN, (pulsesA > 0 || pulsesB > 0) ? HIGH : LOW);

    Serial.print("P27: ");
    Serial.print(flowRateA_LMin, 2);
    Serial.print(" L/min, P26: ");
    Serial.print(flowRateB_LMin, 2);
    Serial.print(" L/min, Combined: ");
    Serial.print(combinedFlowRateLMin, 2);
    Serial.print(" L/min, Total: ");
    Serial.print(totalCombinedMilliLitres);
    Serial.print(" mL / ");
    Serial.print(totalCombinedMilliLitres / 1000.0f, 3);
    Serial.println(" L");

    postReading(SENSOR_A_ID, flowRateA_LMin, totalA_MilliLitres);
    postReading(SENSOR_B_ID, flowRateB_LMin, totalB_MilliLitres);
  }
}
