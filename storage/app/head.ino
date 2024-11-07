#include "Arduino.h"
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
