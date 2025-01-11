#include "Arduino.h"
#include <WiFiClientSecure.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <DHT.h>
#include <time.h>
#include <PZEM004Tv30.h>
#include <PubSubClient.h>

// GPIO Pin
#define LDR_PIN 34
#define PIR_PIN 19
#define DHT_PIN 23
#define LED_PIN 2

// Const value
#define readAndPostInterval 1444
#define ldrInterval 5267
#define dhtInterval 33423
#define pzemInterval 2343

unsigned long currentMillis = 0;
unsigned long readAndPostMillis = 0;
unsigned long touchMillis = 0;
unsigned long ldrMillis = 0;
unsigned long dhtMillis = 0;
unsigned long pzemMillis = 0;
unsigned int pirMinutes;
unsigned int maxPower;

float pzemPower = 0;
float pzemEnergy = 0;
float pzemPowerFactor = 0;
float pzemVa = 0;

bool pirValue;
bool pirHistory;
bool settingUp = 1;
bool pirStatus = 0;
bool pirSchedule = 0;
bool touchHistory[6] = {0};

struct relayData{
	bool status;
	bool automation;
	bool pirAuto;
	bool turnedOn;
	bool turnedOff;
	byte category;
	byte onHour;
	byte onMinute;
	byte offHour;
	byte offMinute;
	byte threshold;
};
relayData relays[6];

byte RELAY_PIN[6] = {22, 21, 32, 33, 25, 26};
byte TOUCH_PIN[6] = {18, 35, 15, 13, 27, 14};
byte ldrValue = 0;
byte lastMonth;
byte dhtHumidity = 0;
byte dhtTemperature = 0;
byte timezone = 7;
byte hourNow = 0;
byte minuteNow = 0;
byte pirOn[2];
byte pirOff[2];
byte pirInterval; // minute

const char ntpServer1[] PROGMEM = "id.pool.ntp.org";

const char ssid[] PROGMEM = "WIFI_SSID";
const char password[] PROGMEM = "WIFI_PASSWORD";
const char token[] PROGMEM = "DEVICE_TOKEN";
const char mqttServer[] PROGMEM = "145.223.21.10";

DHT dht(DHT_PIN, DHT11);
TaskHandle_t fastTrackTask;
TaskHandle_t readAndPostTask;
PZEM004Tv30 pzem(Serial2, 17, 16); // RX, TX || 17, 16
WiFiClient wifiClient;
PubSubClient client(wifiClient);

void initWiFi() {
	WiFi.disconnect();
	WiFi.mode(WIFI_STA);
	WiFi.begin(ssid, password);
	Serial.print("Connecting WiFi...");
	while (WiFi.status() != WL_CONNECTED) {
		Serial.print('.');
		delay(1000);
	}

	WiFi.setAutoReconnect(true);
	Serial.print("\nIP Address: ");
	Serial.println(WiFi.localIP());
}

void setPir(byte hour, byte minute){
	if(pirSchedule == 1){
		byte periodeHour = 0;
		byte hourCounter = pirOn[0];
		while(hourCounter != pirOff[0]){
			hourCounter++;
			periodeHour++;
			if(hourCounter >= 24){
				hourCounter = 0;
			}
		}

		unsigned int startInMinutes = (60 * pirOn[0]) + pirOn[1];
		unsigned int turnOnFor = (periodeHour * 60) + pirOff[1] - pirOn[1]; 
		unsigned int endInMinutes  = (60 * hour) + minute;
		unsigned int turnOffAt = startInMinutes + turnOnFor;

		bool wrapAround = 0;
		if( turnOffAt > (24 * 60)) {
			wrapAround = 1;
		}

		if(!wrapAround) {
			pirStatus = endInMinutes <= (startInMinutes + turnOnFor) && endInMinutes > startInMinutes;
		} else {
			pirStatus = startInMinutes <= endInMinutes && endInMinutes <= (24 * 60);
			
			int upper = (turnOffAt % (24 * 60));
			pirStatus |= endInMinutes >= 0 && endInMinutes < upper;
		}
	} else{
		//pirValue = 1;
		pirStatus = 1;
	}
}

void sendRelayData(byte relayNumber, bool overPower){
  JsonDocument relayDoc;
	relayDoc["token"] = token;
	if(overPower == 0){
		relayDoc["relayNumber"] = relayNumber + 1;
		relayDoc["status"] = relays[relayNumber].status;
		relayDoc["turnedOn"] = relays[relayNumber].turnedOn;
		relayDoc["turnedOff"] = relays[relayNumber].turnedOff;
		relayDoc["overPower"] = 0;
	} else{
		relayDoc["overPower"] = 1;
	}

	char relayJson[150];
	size_t relaySize = serializeJson(relayDoc, relayJson);
	Serial.println(relayJson);

  const char relayTopic[] PROGMEM = "iotsmartkos/relay";
  client.publish(relayTopic, relayJson, relaySize);
    
  Serial.print("Relay ");
  Serial.print(relayNumber + 1);
  Serial.println(F(" Sent"));
}

void postData(String category, float sendData){
  JsonDocument sensorDoc;
    
	sensorDoc["token"] = token;
	sensorDoc["category"] = category;
	sensorDoc["value"] = sendData;
  if(category == "pzem-energy"){
		struct tm timeinfo;
    getLocalTime(&timeinfo);
	  sensorDoc["day"] = timeinfo.tm_mday;
    sensorDoc["month"] = (timeinfo.tm_mon + 1);
    sensorDoc["year"] = (1900 + timeinfo.tm_year);
    sensorDoc["hour"] = timeinfo.tm_hour;
  }

	char sensorJson[150];
	size_t n = serializeJson(sensorDoc, sensorJson);
	Serial.println(sensorJson);

  const char sensorTopic[] PROGMEM = "iotsmartkos/sensor";
  client.publish(sensorTopic, sensorJson, n);
	// client.loop();

	Serial.print(category);
	Serial.println(F(" data sent"));
}

void getRelayData(){
  WiFiClientSecure *client = new WiFiClientSecure;
  client->setInsecure();
	settingUp = 1;
	HTTPClient initialHttp;
	
	JsonDocument setupJson;
	setupJson["token"] = token;

	String initialData;
	serializeJson(setupJson, initialData);
	Serial.println(initialData);
	
	int responseCode;
	while(responseCode != 200){
		Serial.print("getRelayData: ");
		const char relayHttp[] PROGMEM = "https://smart-kos.site/api/getrelaydata";
		const char contentType[] PROGMEM = "Content-Type";
		const char jsonHeader[] PROGMEM = "application/json";
		initialHttp.begin(*client, relayHttp);
		initialHttp.addHeader(contentType, jsonHeader);

		responseCode = initialHttp.POST(initialData);
		Serial.println(responseCode);
	}

  JsonDocument initialResponse;
	DeserializationError error = deserializeJson(initialResponse, initialHttp.getString());
	if (error) {
		Serial.print(F("deserializeJson() failed: "));
		Serial.println(error.c_str());
		return;
	}

  timezone = initialResponse["timezone"].as<byte>();
	lastMonth = initialResponse["lastMonth"].as<byte>();
	int lastYear = initialResponse["lastYear"].as<int>();
	int lastDay = initialResponse["lastDay"].as<int>();
	hourNow = initialResponse["lastHour"].as<byte>();
	maxPower = initialResponse["maxPower"].as<int>();
	pirInterval = initialResponse["pirInterval"].as<byte>();
	pirSchedule = initialResponse["pirSchedule"].as<bool>();
	pirHistory = initialResponse["pirStatus"].as<bool>();
	pirOn[0] = initialResponse["pirOnHour"].as<byte>();
	pirOn[1] = initialResponse["pirOnMin"].as<byte>();
	pirOff[0] = initialResponse["pirOffHour"].as<byte>();
	pirOff[1] = initialResponse["pirOffMin"].as<byte>();

	for(byte i=0; i<6; ++i){
		relays[i] = {
			initialResponse["status"][i].as<bool>(),
			initialResponse["automation"][i].as<bool>(),
			initialResponse["pirAuto"][i].as<bool>(),
			initialResponse["turnedOn"][i].as<bool>(),
			initialResponse["turnedOff"][i].as<bool>(),
			initialResponse["category"][i].as<byte>(),
			initialResponse["onHour"][i].as<byte>(),
			initialResponse["onMinute"][i].as<byte>(),
			initialResponse["offHour"][i].as<byte>(),
			initialResponse["offMinute"][i].as<byte>(),
			initialResponse["threshold"][i].as<byte>(),
		};
    digitalWrite(RELAY_PIN[i], relays[i].status);
    delay(500);
	}

  Serial.println(F("DIGITAL WRITE SUCCESS"));
	configTime(timezone*3600, 0, ntpServer1);
	struct tm timeinfo;
	getLocalTime(&timeinfo);
			
	Serial.print("timezone: ");
	Serial.println(timezone);

	Serial.print("Current Time: ");
	Serial.println(&timeinfo, "%T %F");
	pirValue = 1;
	pirMinutes = ((timeinfo.tm_min + pirInterval + 1) % 60);

	setPir(timeinfo.tm_hour, timeinfo.tm_min);

	if(isnan(lastYear)){
		Serial.println(F("New User, resetting PZEM"));
		pzem.resetEnergy();
	} else {
		if(hourNow == (timeinfo.tm_hour - 1) && lastDay == (timeinfo.tm_mday) && lastMonth == (timeinfo.tm_mon + 1) && lastYear == (1900 + timeinfo.tm_year)){
			Serial.println(F("Continue PZEM"));
		} else {
			Serial.println(F("Long ago, resetting PZEM"));
			pzem.resetEnergy();
		}
	}

	initialHttp.end();
}

void setTimeBased(byte hour, byte minute){
	for(byte i=0; i<6; ++i){
		if(relays[i].category != 2 && relays[i].category != 3 && relays[i].automation == 1){
			relays[i].turnedOn = 0;
			relays[i].turnedOff = 0;
			if(relays[i].onHour == hour && relays[i].onMinute == minute){
					if(relays[i].status != 1){
						if(pirStatus == 1){
							if((relays[i].pirAuto == 1 && pirValue == 1) || (relays[i].pirAuto == 0)){
								relays[i].status = 1;
								Serial.println(F("Time ON"));
								digitalWrite(RELAY_PIN[i], relays[i].status);
								sendRelayData(i, false);
							}
						} else{
							relays[i].status = 1;
							Serial.println(F("Time ON"));
							digitalWrite(RELAY_PIN[i], relays[i].status);
							sendRelayData(i, false);
						}
					}
			} else if(relays[i].offHour == hour && relays[i].offMinute == minute){
				if(relays[i].status != 0){
					relays[i].status = 0;
					digitalWrite(RELAY_PIN[i], relays[i].status);
					Serial.println(F("Time OFF"));
			    sendRelayData(i, false);
				}
			}
		}
	}
}

void setFan(byte temperatureRead){
	for(byte i=0; i<6; ++i){
		if(relays[i].category == 2 && relays[i].automation == 1){
			if(temperatureRead >= relays[i].threshold){
				if(relays[i].turnedOff == 0){
					if(relays[i].turnedOn != 0){
						relays[i].turnedOn = 0;
						Serial.println(F("fan turnedOn = 0"));
				        sendRelayData(i, false);
					}
					if(pirStatus == 1){
						if((relays[i].pirAuto == 1 && pirValue != 0) || (relays[i].pirAuto == 0)){
							if(relays[i].status != 1){
								relays[i].status = 1;
								digitalWrite(RELAY_PIN[i], relays[i].status);
								Serial.println(F("Kipas ON"));
								sendRelayData(i, false);
							}
						}
					} else{
						if(relays[i].status != 1){
							relays[i].status = 1;
							digitalWrite(RELAY_PIN[i], relays[i].status);
							Serial.println(F("Kipas ON"));
							sendRelayData(i, false);
						}
					}
				}
			} else{
				if(relays[i].turnedOn == 0){
					if(relays[i].turnedOff != 0){
						relays[i].turnedOff = 0;
						Serial.println(F("fan turnedOff = 0"));
					    sendRelayData(i, false);
					}
					if(relays[i].status != 0){
						relays[i].status = 0;
						digitalWrite(RELAY_PIN[i], relays[i].status);
						Serial.println(F("Kipas OFF"));
					    sendRelayData(i, false);
					}
				}
			}
		}
	}
}

void setLamp(byte ldrRead){
	for(byte i=0; i<6; ++i){
		if(relays[i].category == 3 && relays[i].automation == 1){
			if(ldrRead >= relays[i].threshold){
				if(relays[i].turnedOn == 0){
					if(relays[i].turnedOff != 0){
						relays[i].turnedOff = 0;
						Serial.println(F("lamp turnedOff = 0"));
					    sendRelayData(i, false);
					}
					if(relays[i].status != 0){
						relays[i].status = 0;
						digitalWrite(RELAY_PIN[i], relays[i].status);
						Serial.println(F("Lampu OFF"));
					  sendRelayData(i, false);
					}
				}
			} else{
				if(relays[i].turnedOff == 0){
					if(relays[i].turnedOn != 0){
						relays[i].turnedOn = 0;
						Serial.println(F("lamp turnedOn = 0"));
				    sendRelayData(i, false);
					}
					if(pirStatus == 1){
						if((relays[i].pirAuto == 1 && pirValue != 0) || (relays[i].pirAuto == 0)){
							if(relays[i].status != 1){
								relays[i].status = 1;
								digitalWrite(RELAY_PIN[i], relays[i].status);
								Serial.println(F("Lampu ON"));
								sendRelayData(i, false);
							}
						}
					} else{
						if(relays[i].status != 1){
							relays[i].status = 1;
							digitalWrite(RELAY_PIN[i], relays[i].status);
							Serial.println(F("Lampu ON"));
							sendRelayData(i, false);
						}
					}
				}
			}
		}
	}
}

void callback(char* topic, byte* payload, unsigned int length) {
	// Channel:
	// 0 = Setup,
	// 1 = ON/OFF automation & relay,
	// 2 = automation edit,
	// 3 = PIR automation switch,
	// 4 = Pir automation,
	// 5 = Max Power,

  Serial.print("Callback Core: ");
  Serial.println(xPortGetCoreID());
    
  JsonDocument messageJson;
  deserializeJson(messageJson, payload, length);

  byte channel = messageJson["channel"].as<byte>();
	byte relayNumber;
	if(channel == 1){
		relayNumber = (messageJson["number"].as<byte>() - 1);
    relays[relayNumber].status = messageJson["status"].as<bool>();
    relays[relayNumber].automation = messageJson["automation"].as<bool>();
    relays[relayNumber].turnedOn = messageJson["turnedOn"].as<bool>();
    relays[relayNumber].turnedOff = messageJson["turnedOff"].as<bool>();

    digitalWrite(RELAY_PIN[relayNumber], relays[relayNumber].status);
	} else if(channel == 2){
		relayNumber = (messageJson["number"].as<byte>() - 1);
      relays[relayNumber].category = messageJson["category"].as<byte>();
		if(relays[relayNumber].category == 1){ // Time
	    relays[relayNumber].onHour = messageJson["onHour"].as<byte>();
	    relays[relayNumber].onMinute = messageJson["onMin"].as<byte>();
	    relays[relayNumber].offHour = messageJson["offHour"].as<byte>();
	    relays[relayNumber].offMinute = messageJson["offMin"].as<byte>();
		} else if(relays[relayNumber].category == 2 || relays[relayNumber].category == 3){ // Suhu
	    relays[relayNumber].threshold = messageJson["threshold"].as<byte>();
			if(relays[relayNumber].category == 2){
				DHT dht(DHT_PIN, DHT11);
				byte temperatureRead = dht.readTemperature();
				setFan(temperatureRead);
			} else if(relays[relayNumber].category == 3){
				byte ldrRead = (map(analogRead(LDR_PIN), 4095, 0, 0, 100));
				setLamp(ldrRead);
			}
		}
	} else if(channel == 3){
		relayNumber = (messageJson["number"].as<byte>() - 1);
	    relays[relayNumber].pirAuto = messageJson["pirAuto"].as<bool>();
		if(relays[relayNumber].pirAuto == 1){
			if(pirValue == 0 && pirStatus == 1){
				relays[relayNumber].status = 0;
				sendRelayData(relayNumber, false);
			}
		}	
	} else if(channel == 4){
		pirSchedule = messageJson["status"].as<bool>();
		pirInterval = messageJson["interval"].as<byte>();
		pirOn[0] = messageJson["onHour"].as<byte>();
		pirOn[1] = messageJson["onMin"].as<byte>();
		pirOff[0] = messageJson["offHour"].as<byte>();
		pirOff[1] = messageJson["offMin"].as<byte>();
		
		struct tm timeinfo;
    getLocalTime(&timeinfo);
		setPir(timeinfo.tm_hour, timeinfo.tm_min);
		pirMinutes = ((timeinfo.tm_min + pirInterval + 1) % 60);
	} else if(channel == 5){
		maxPower = messageJson["maxPower"].as<int>();
	}

	if(channel != 0){
		char responseJson[256];
		serializeJson(messageJson, responseJson);
		Serial.print("Message :");
		Serial.println(responseJson);
	}
}

void reconnect() {
  client.setServer(mqttServer, 1883);
  Serial.print("Connecting MQTT...");
  while (!client.connected()) {
    if (client.connect(token)) {
      client.subscribe(token, 1);
      client.setCallback(callback);
            
      Serial.println(F("connected"));
      Serial.print("clientId: ");
      Serial.println(token);
		  client.loop();
    } else {
      Serial.print("failed, rc = ");
      Serial.print(client.state());
      Serial.println(F(" try again in 5 seconds"));
      delay(5000);
    }
  }
}

void fastTrack(void* pvParameters){ 
	struct tm timeinfo;
	for(;;){
		getLocalTime(&timeinfo);
		currentMillis = millis();
		if(currentMillis - touchMillis >= 200){
			bool pirNow = digitalRead(PIR_PIN);
			if(pirNow > 0){
				if(pirHistory == 0){
					postData("pir", 1);
					pirHistory = 1;
				}
				pirValue = 1;
				pirMinutes = ((timeinfo.tm_min + pirInterval + 1) % 60);
			}
			bool touchValue[6];
			touchValue[5] = digitalRead(TOUCH_PIN[5]);
			touchValue[4] = digitalRead(TOUCH_PIN[4]);
			touchValue[3] = digitalRead(TOUCH_PIN[3]);
			touchValue[2] = digitalRead(TOUCH_PIN[2]);
			touchValue[1] = digitalRead(TOUCH_PIN[1]);
			touchValue[0] = digitalRead(TOUCH_PIN[0]);
			if(touchValue[5] == 1 && touchHistory[5] == 0){
				pirMinutes = ((timeinfo.tm_min + pirInterval + 1) % 60);
				if(relays[5].automation == 1){
					if(relays[5].status == 1){
						if(relays[5].turnedOff == 0){
							relays[5].turnedOff = 1;
						} else{
							relays[5].turnedOff = 0;
						}
						relays[5].turnedOn = 0;
					} else{
						if(relays[5].turnedOn == 0){
							relays[5].turnedOn = 1;
						} else{
							relays[5].turnedOn = 0;
						}
						relays[5].turnedOff = 0;
					}
				}
				relays[5].status = !relays[5].status;
				digitalWrite(RELAY_PIN[5], relays[5].status);
				Serial.println(F("6 Touched"));
				sendRelayData(5, false);
			} else if(touchValue[4] == 1 && touchHistory[4] == 0){
				pirMinutes = ((timeinfo.tm_min + pirInterval + 1) % 60);
				if(relays[4].automation == 1){
					if(relays[4].status == 1){
						if(relays[4].turnedOff == 0){
							relays[4].turnedOff = 1;
						} else{
							relays[4].turnedOff = 0;
						}
						relays[4].turnedOn = 0;
					} else{
						if(relays[4].turnedOn == 0){
							relays[4].turnedOn = 1;
						} else{
							relays[4].turnedOn = 0;
						}
						relays[4].turnedOff = 0;
					}
				}
				relays[4].status = !relays[4].status;
				digitalWrite(RELAY_PIN[4], relays[4].status);
				Serial.println(F("5 Touched"));
				sendRelayData(4, false);
			} else if(touchValue[3] == 1 && touchHistory[3] == 0){
				pirMinutes = ((timeinfo.tm_min + pirInterval + 1) % 60);
				if(relays[3].automation == 1){
					if(relays[3].status == 1){
						if(relays[3].turnedOff == 0){
							relays[3].turnedOff = 1;
						} else{
							relays[3].turnedOff = 0;
						}
						relays[3].turnedOn = 0;
					} else{
						if(relays[3].turnedOn == 0){
							relays[3].turnedOn = 1;
						} else{
							relays[3].turnedOn = 0;
						}
						relays[3].turnedOff = 0;
					}
				}
				relays[3].status = !relays[3].status;
				digitalWrite(RELAY_PIN[3], relays[3].status);
				Serial.println(F("4 Touched"));
				sendRelayData(3, false);
			} else if(touchValue[2] == 1 && touchHistory[2] == 0){
				pirMinutes = ((timeinfo.tm_min + pirInterval + 1) % 60);
				if(relays[2].automation == 1){
					if(relays[2].status == 1){
						if(relays[2].turnedOff == 0){
							relays[2].turnedOff = 1;
						} else{
							relays[2].turnedOff = 0;
						}
						relays[2].turnedOn = 0;
					} else{
						if(relays[2].turnedOn == 0){
							relays[2].turnedOn = 1;
						} else{
							relays[2].turnedOn = 0;
						}
						relays[2].turnedOff = 0;
					}
				}
				relays[2].status = !relays[2].status;
				digitalWrite(RELAY_PIN[2], relays[2].status);
				Serial.println(F("3 Touched"));
				sendRelayData(2, false);
			} else if(touchValue[1] == 1 && touchHistory[1] == 0){
				pirMinutes = ((timeinfo.tm_min + pirInterval + 1) % 60);
				if(relays[1].automation == 1){
					if(relays[1].status == 1){
						if(relays[1].turnedOff == 0){
							relays[1].turnedOff = 1;
						} else{
							relays[1].turnedOff = 0;
						}
						relays[1].turnedOn = 0;
					} else{
						if(relays[1].turnedOn == 0){
							relays[1].turnedOn = 1;
						} else{
							relays[1].turnedOn = 0;
						}
						relays[1].turnedOff = 0;
					}
				}
				relays[1].status = !relays[1].status;
				digitalWrite(RELAY_PIN[1], relays[1].status);
				Serial.println(F("2 Touched"));
				sendRelayData(1, false);
			} else if(touchValue[0] == 1 && touchHistory[0] == 0){
				pirMinutes = ((timeinfo.tm_min + pirInterval + 1) % 60);
				if(relays[0].automation == 1){
					if(relays[0].status == 1){
						if(relays[0].turnedOff == 0){
							relays[0].turnedOff = 1;
						} else{
							relays[0].turnedOff = 0;
						}
						relays[0].turnedOn = 0;
					} else{
						if(relays[0].turnedOn == 0){
							relays[0].turnedOn = 1;
						} else{
							relays[0].turnedOn = 0;
						}
						relays[0].turnedOff = 0;
					}
				}
				relays[0].status = !relays[0].status;
				digitalWrite(RELAY_PIN[0], relays[0].status);
				Serial.println(F("1 Touched"));
				sendRelayData(0, false);
			}

			touchHistory[5] = touchValue[5];
			touchHistory[4] = touchValue[4];
			touchHistory[3] = touchValue[3];
			touchHistory[2] = touchValue[2];
			touchHistory[1] = touchValue[1];
			touchHistory[0] = touchValue[0];
			touchMillis = currentMillis;
		}
	}
}

void readAndPost(void* pvParameters){
	struct tm timeinfo;
	for(;;){
		getLocalTime(&timeinfo);
		if(WiFi.status() != WL_CONNECTED){
			digitalWrite(LED_PIN, 0);
		} else{
			if(!client.connected()){
				digitalWrite(LED_PIN, 0);
				getRelayData();
				reconnect();
			} else{
				client.loop();
				digitalWrite(LED_PIN, 1);
				if(settingUp == 1){
					postData("temperature", dht.readTemperature());
					postData("humidity", dht.readHumidity());
					postData("ldr", (map(analogRead(LDR_PIN), 4095, 0, 0, 100)));
					settingUp = 0;
				}
				if((currentMillis - dhtMillis) >= dhtInterval){
					// DHT (33423 ms)
					DHT dht(DHT_PIN, DHT11);

					byte temperatureRead = dht.readTemperature();
					setFan(temperatureRead);
					if(dhtTemperature != temperatureRead){
						postData("temperature", temperatureRead);
						dhtTemperature = temperatureRead;
					}

					byte humidityRead = dht.readHumidity();
					if(dhtHumidity != humidityRead){
						postData("humidity", humidityRead);
						dhtHumidity = humidityRead;
					}
					postData("pir", pirValue);
					dhtMillis = currentMillis;
				}
				if((currentMillis - ldrMillis) >= ldrInterval) {
					// LDR (5267 ms)
					byte ldrRead = (map(analogRead(LDR_PIN), 4095, 0, 0, 100));
					setLamp(ldrRead);
					if(ldrValue != ldrRead){
						postData("ldr", ldrRead);
						ldrValue = ldrRead;
					}
					ldrMillis = currentMillis;
				}
				if((currentMillis - pzemMillis) >= pzemInterval) {
					//PZEM (2343 ms)
					pzemPower = pzem.power();
					pzemPowerFactor = pzem.pf();
					pzemVa = (pzemPower / pzemPowerFactor);
					if(isnan(pzemVa) || pzemVa == NULL){
						postData("pzem-apparentPower", 0);
					} else{
						postData("pzem-apparentPower", pzemVa);
					}
					if(pzemVa > maxPower){
						for(byte i=0; i<6; i++){
							relays[i].status = 0;
							relays[i].turnedOff = 1;
							relays[i].turnedOn = 0;
							digitalWrite(RELAY_PIN[i], relays[i].status);
							delay(100);
						}
						sendRelayData(0, true);
					}
					pzemMillis = currentMillis;
				}
				if((currentMillis - readAndPostMillis) >= readAndPostInterval) {
					// Send PIR & get Relay (1444 ms)
					pzemEnergy = (pzem.energy() / 1000);
					pzemPower = pzem.power();
					Serial.print("currentTime: ");
					Serial.println(&timeinfo, "%T");
					Serial.print("pirMinutes: ");
					Serial.println(pirMinutes);
					if(pirValue == 1 && timeinfo.tm_min == pirMinutes){
						pirValue = 0;
						pirHistory = 0;
						postData("pir", pirValue);
						Serial.println(F("PIR OFF"));
						if(pirStatus == 1){
							for(byte i=0; i<6; ++i){
								if(relays[i].pirAuto == 1){
									if(relays[i].status != 0){
										relays[i].turnedOff = 0;
										relays[i].turnedOn = 0;
										relays[i].status = 0;
										digitalWrite(RELAY_PIN[i], relays[i].status);
										Serial.print("Relay ");
										Serial.print(i+1);
										Serial.println(F(" Turned Off (PIR)"));
										sendRelayData(i, false);
										delay(500);
									}
								}
							}
						}
					}
					if(timeinfo.tm_hour != hourNow){
						configTime(timezone*3600, 0, ntpServer1);
						hourNow = timeinfo.tm_hour;
						Serial.println(F("hour changed"));
						pzemEnergy = (pzem.energy() / 1000);
						postData("pzem-energy", pzemEnergy);
						pzem.resetEnergy();
					}
					if(timeinfo.tm_min != minuteNow){
						Serial.println(F("minute changed"));
						minuteNow = timeinfo.tm_min;
						setPir(hourNow, minuteNow);
						Serial.print("pirStatus: ");
						Serial.println(pirStatus);
						setTimeBased(hourNow, minuteNow);
					}
					readAndPostMillis = currentMillis;
				}
			}
		}
	}
}

void setup() {
	disableCore0WDT();
	delay(500);
	disableCore1WDT();
	delay(500);
	Serial.begin(115200);
	delay(500);
	
	pinMode(PIR_PIN, INPUT);
	pinMode(LDR_PIN, ANALOG);
	pinMode(LED_PIN, OUTPUT);
	digitalWrite(LED_PIN, 0);

	for(byte i=0; i<6; ++i){
		pinMode(TOUCH_PIN[i], INPUT);
		Serial.print(F("status above is touch_pin "));
		Serial.println(i + 1);
		delay(500);
		pinMode(RELAY_PIN[i], OUTPUT);
		Serial.print(F("status above is relay_pin "));
		Serial.println(i + 1);
		delay(500);
	}
		
	dht.begin();
	initWiFi();
	configTime(timezone*3600, 0, ntpServer1);
	delay(500);

	xTaskCreatePinnedToCore(
		fastTrack,
		"fastTrack",
		10000,
		NULL,
		1,
		&fastTrackTask,
		1
	);                              
    Serial.println(F("FAST TRACK CREATED"));
	delay(500);

	xTaskCreatePinnedToCore(
		readAndPost, // function
		"readAndPost", // name
		10000, // Stack size
		NULL, // parameter
		1, // priority
		&readAndPostTask, // Handler
		0 // CORE 0
	);
    Serial.println(F("READ AND POST CREATED"));
}

void loop(){
    //
}
