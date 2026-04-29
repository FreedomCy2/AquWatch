/*
  ESP32 + Rain Sensor (analog)
  Direct Wi-Fi HTTP posting to Laravel API.
*/

#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>

const int SENSOR_PIN = 34;

// Wi-Fi credentials
const char* WIFI_SSID = "DecoA";
const char* WIFI_PASSWORD = "315321TKB";

// Laravel API settings
const char* API_URL = "https://aquwatch.org/api/ingest/rain";
const char* SENSOR_TOKEN = "aqw_1f8d7a9b3c4e6f2a91d0b7e5c3a8f6d4b2c9e1a7f3d5b8c0";
const char* SENSOR_ID = "rain-esp32-01";

const uint32_t SAMPLE_INTERVAL_MS = 1000;
const uint32_t WIFI_RETRY_MS = 10000;

uint32_t lastSampleMs = 0;
uint32_t lastWifiRetryMs = 0;
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

const char* classifyRain(int value) {
  if (value > 2800) {
    return "no_rain";
  }
  if (value > 2100) {
    return "rain";
  }
  return "heavy_rain";
}

void postRainReading(int analogValue, const char* intensityLevel) {
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

  String body = "{";
  body += "\"sensor_id\":\"" + String(SENSOR_ID) + "\",";
  body += "\"analog_value\":" + String(analogValue) + ",";
  body += "\"intensity_level\":\"" + String(intensityLevel) + "\"";

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
  Serial.println("\n[Boot] ESP32 rain sender starting...");
  Serial.print("[Boot] API URL: ");
  Serial.println(API_URL);

  pinMode(SENSOR_PIN, INPUT);

  WiFi.mode(WIFI_STA);
  WiFi.setAutoReconnect(true);
  WiFi.setSleep(false);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  Serial.println("[WiFi] Initial connect request sent.");

  lastSampleMs = millis();
}

void loop() {
  connectWifiIfNeeded();

  uint32_t now = millis();
  if (now - lastSampleMs < SAMPLE_INTERVAL_MS) {
    return;
  }

  lastSampleMs = now;

  int total = 0;
  for (int i = 0; i < 10; i++) {
    total += analogRead(SENSOR_PIN);
    delay(10);
  }

  int value = total / 10;
  const char* level = classifyRain(value);

  Serial.print("Value: ");
  Serial.print(value);
  Serial.print(" -> ");

  if (strcmp(level, "no_rain") == 0) {
    Serial.println("No Rain");
  } else if (strcmp(level, "rain") == 0) {
    Serial.println("Rain");
  } else {
    Serial.println("Heavy Rain");
  }

  postRainReading(value, level);
}
