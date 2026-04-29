/*
  ESP32 Flood Monitoring + Direct HTTP posting to Laravel API.
  Based on 3 digital flood level sensors (active LOW with INPUT_PULLUP).
*/

#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>

const int S1 = 18;
const int S2 = 19;
const int S3 = 21;

const int GREEN_LED = 25;
const int YELLOW_LED = 26;
const int RED_LED = 27;

const char* WIFI_SSID = "DecoA";
const char* WIFI_PASSWORD = "315321TKB";

const char* API_URL = "https://aquwatch.org/api/ingest/flood";
const char* SENSOR_TOKEN = "aqw_1f8d7a9b3c4e6f2a91d0b7e5c3a8f6d4b2c9e1a7f3d5b8c0";
const char* SENSOR_ID = "flood-esp32-01";

const unsigned long RESET_DELAY_MS = 5000;
const unsigned long SAMPLE_INTERVAL_MS = 1000;
const unsigned long WIFI_RETRY_MS = 10000;

unsigned long t1 = 0;
unsigned long t2 = 0;
unsigned long t3 = 0;
unsigned long lastTimeWet = 0;
unsigned long lastSampleMs = 0;
unsigned long lastWifiRetryMs = 0;

bool s1Triggered = false;
bool s2Triggered = false;
bool s3Triggered = false;

String lastStatus = "SAFE / DRY";
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

void setLeds(bool g, bool y, bool r) {
  digitalWrite(GREEN_LED, g ? HIGH : LOW);
  digitalWrite(YELLOW_LED, y ? HIGH : LOW);
  digitalWrite(RED_LED, r ? HIGH : LOW);
}

void updateSystem(const String& status) {
  if (status == "CRITICAL") {
    setLeds(false, false, true);
  } else if (status == "FLASH FLOOD WARNING") {
    setLeds(false, true, false);
  } else if (status == "NORMAL RISE" || status == "LEVEL 1 DETECTED") {
    setLeds(true, false, false);
  } else {
    setLeds(false, false, false);
  }

  lastStatus = status;
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

  unsigned long now = millis();
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

void postFloodReading(const String& status, bool s1Wet, bool s2Wet, bool s3Wet, unsigned long riseTimeSec) {
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
  body += "\"status\":\"" + status + "\",";
  body += "\"s1_wet\":" + String(s1Wet ? 1 : 0) + ",";
  body += "\"s2_wet\":" + String(s2Wet ? 1 : 0) + ",";
  body += "\"s3_wet\":" + String(s3Wet ? 1 : 0) + ",";
  body += "\"rise_time_sec\":" + String((unsigned long) riseTimeSec);
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
  Serial.println("\n[Boot] ESP32 flood sender starting...");
  Serial.print("[Boot] API URL: ");
  Serial.println(API_URL);

  pinMode(S1, INPUT_PULLUP);
  pinMode(S2, INPUT_PULLUP);
  pinMode(S3, INPUT_PULLUP);

  pinMode(GREEN_LED, OUTPUT);
  pinMode(YELLOW_LED, OUTPUT);
  pinMode(RED_LED, OUTPUT);
  setLeds(false, false, false);

  WiFi.mode(WIFI_STA);
  WiFi.setAutoReconnect(true);
  WiFi.setSleep(false);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  Serial.println("[WiFi] Initial connect request sent.");

  lastSampleMs = millis();
  Serial.println("Flood monitor started.");
}

void loop() {
  connectWifiIfNeeded();

  unsigned long now = millis();
  if (now - lastSampleMs < SAMPLE_INTERVAL_MS) {
    return;
  }
  lastSampleMs = now;

  int s1 = digitalRead(S1);
  int s2 = digitalRead(S2);
  int s3 = digitalRead(S3);

  bool s1Wet = (s1 == LOW);
  bool s2Wet = (s2 == LOW);
  bool s3Wet = (s3 == LOW);

  if (s1Wet || s2Wet || s3Wet) {
    lastTimeWet = now;
  }

  if (s1Wet && !s1Triggered) { t1 = now; s1Triggered = true; }
  if (s2Wet && !s2Triggered) { t2 = now; s2Triggered = true; }
  if (s3Wet && !s3Triggered) { t3 = now; s3Triggered = true; }

  unsigned long riseTimeSec = 0;
  if (s1Triggered && s2Triggered && t2 >= t1) {
    riseTimeSec = (t2 - t1) / 1000;
  }

  if (s3Triggered) {
    updateSystem("CRITICAL");
  } else if (s2Triggered) {
    if (riseTimeSec > 0 && riseTimeSec < 5) {
      updateSystem("FLASH FLOOD WARNING");
    } else {
      updateSystem("NORMAL RISE");
    }
  } else if (s1Triggered) {
    updateSystem("LEVEL 1 DETECTED");
  } else {
    updateSystem("SAFE / DRY");
  }

  if (!s1Wet && !s2Wet && !s3Wet && (now - lastTimeWet > RESET_DELAY_MS)) {
    if (s1Triggered || s2Triggered || s3Triggered) {
      s1Triggered = false;
      s2Triggered = false;
      s3Triggered = false;
      t1 = t2 = t3 = 0;
      updateSystem("SAFE / DRY");
    }
  }

  Serial.print("STATUS:");
  Serial.print(lastStatus);
  Serial.print(",S1:");
  Serial.print(s1Wet ? 1 : 0);
  Serial.print(",S2:");
  Serial.print(s2Wet ? 1 : 0);
  Serial.print(",S3:");
  Serial.print(s3Wet ? 1 : 0);
  Serial.print(",RISE_S:");
  Serial.println(riseTimeSec);

  postFloodReading(lastStatus, s1Wet, s2Wet, s3Wet, riseTimeSec);
}
