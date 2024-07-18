<?php

namespace App\Console\Commands;

use App\Events\PowerIsOver;
use App\Events\RelayStatusUpdated;
use App\Events\RoomBillUpdated;
use App\Events\SensorDataUpdated;
use App\Models\IotDevice;
use App\Models\KwhHistory;
use App\Models\Relay;
use App\Models\Room;
use App\Models\RoomBill;
use App\Models\SensorData;
use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;

class MqttSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:subscribe';
    public $mqtt;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to MQTT topic';

    public function updateData(string $topic, string $message){
        $jsonDoc = json_decode($message, true);

        // Relay
        if($topic == 'iotsmartkos/relay'){
            $deviceId = IotDevice::where('token', $jsonDoc['token'])->value('id');
            if($jsonDoc['overPower'] == 1){
                PowerIsOver::dispatch($deviceId);
                $relayUpdate = Relay::where('deviceId', $deviceId)->update([
                    'status' => false,
                    'turnedOff' => true,
                    'turnedOn' => false,
                ]);
                if(!$relayUpdate){
                    echo "Overpower Error";
                }
            } else{
                if(isset($jsonDoc['token']) && isset($jsonDoc['relayNumber'])){
                    RelayStatusUpdated::dispatch($deviceId, $jsonDoc['relayNumber'], $jsonDoc['status']);
                    if(!$deviceId){
                        echo 'Device not found';
                        echo PHP_EOL;
                    } else{
                        $relayId = Relay::where([['deviceId', $deviceId], ['number', $jsonDoc['relayNumber']]])->value('id');
                        if(!$relayId){
                            echo 'Relay not found';
                            echo PHP_EOL;
                        } else{
                            if(isset($jsonDoc['status'])){
                                Relay::where('id', $relayId)->update(['status' => $jsonDoc['status']]);
                            }
                            if(isset($jsonDoc['turnedOn'])){
                                Relay::where('id', $relayId)->update(['turnedOn' => $jsonDoc['turnedOn']]);
                            }
                            if(isset($jsonDoc['turnedOff'])){
                                Relay::where('id', $relayId)->update(['turnedOff' => $jsonDoc['turnedOff']]);
                            }
                        }
                    }
                } else{
                    echo 'error';
                }
            }
        // Sensor
        } else if($topic == 'iotsmartkos/sensor'){
            if(isset($jsonDoc['token']) && isset($jsonDoc['category']) && isset($jsonDoc['value'])){
                $deviceId = IotDevice::where('token', $jsonDoc['token'])->value('id');
                // echo "deviceId = ".$deviceId;
                if(!$deviceId){
                    echo 'Device not found';
                    echo PHP_EOL;
                } else{
                    if($jsonDoc['category'] == 'pzem-energy'){
                        if(isset($jsonDoc['day']) && isset($jsonDoc['month']) && isset($jsonDoc['year']) && isset($jsonDoc['hour'])){
                            if($jsonDoc['hour'] == 0){
                                if($jsonDoc['day'] == 1){
                                    if($jsonDoc['month'] != 1){
                                        $jsonDoc['month'] = $jsonDoc['month']-1;
                                        $monthEnd = (int)date("t", strtotime($jsonDoc['year'] .'-'. $jsonDoc['month']));
                                    } else{
                                        $jsonDoc['month'] = 12;
                                        $jsonDoc['year'] = $jsonDoc['year']-1;
                                        $monthEnd = (int)date("t", strtotime($jsonDoc['year'] .'-12'));
                                    }
                                    $jsonDoc['day'] = $monthEnd;
                                } else{
                                    $jsonDoc['day'] = $jsonDoc['day']-1;
                                }
                                $jsonDoc['hour'] = 24;
                            }
                            $roomId = IotDevice::where('id', $deviceId)->value('roomId');
                            $userId = Room::where('id', $roomId)->value('userId');
                            $roomTariff = (float)Room::where([['userId', $userId], ['id', $roomId]])->value('tariff');
                            $previousKwh = KwhHistory::where([['userId', $userId], ['roomId', $roomId], ['day', $jsonDoc['day']], ['month', $jsonDoc['month']], ['year', $jsonDoc['year']]])->orderBy('id', 'desc')->value('kwh');
                            if($previousKwh === null){
                                $previousKwh = (float)$jsonDoc['value'];
                            } else{
                                $previousKwh += (float)$jsonDoc['value'];
                            }
                            $roomBill = (float)$previousKwh * (float)$roomTariff;
                            RoomBillUpdated::dispatch($deviceId, $jsonDoc['hour'], $jsonDoc['value'], $roomBill);
                            KwhHistory::insert([
                                'userId' => $userId,
                                'roomId' => $roomId,
                                'day' => $jsonDoc['day'],
                                'month' => $jsonDoc['month'],
                                'year' => $jsonDoc['year'],
                                'hour' => $jsonDoc['hour'],
                                'tariff' => $roomTariff,
                                'kwh' => 0 + (float)$previousKwh,
                                'bill' => 0 + (float)$roomBill,
                            ]);
                            $monthEnd = (int)date("t", strtotime($jsonDoc['year'] .'-'. $jsonDoc['month']));
                            $monthlyKwh = KwhHistory::where([['userId', $userId], ['roomId', $roomId], ['day', $monthEnd+1], ['month', $jsonDoc['month']], ['year', $jsonDoc['year']], ['hour', 24]])->value('kwh');
                            if($monthlyKwh === null){
                                $monthlyKwh = (float)$jsonDoc['value'];
                            } else{
                                $monthlyKwh += (float)$jsonDoc['value'];
                                KwhHistory::where([['userId', $userId], ['roomId', $roomId], ['day', $monthEnd+1], ['month', $jsonDoc['month']], ['year', $jsonDoc['year']], ['hour', 24]])->delete();
                            }
                            $monthlyBill = (float)$monthlyKwh * (float)$roomTariff;
                            KwhHistory::insert([
                                'userId' => $userId,
                                'roomId' => $roomId,
                                'day' => $monthEnd+1,
                                'month' => $jsonDoc['month'],
                                'year' => $jsonDoc['year'],
                                'hour' => 24,
                                'tariff' => $roomTariff,
                                'kwh' => (float)$monthlyKwh,
                                'bill' => (float)$monthlyBill,
                            ]);

                            // RoomBill::where([['userId', $userId], ['roomId', $roomId]])->update(['bill' => $roomBill]);
                        }
                    } else{
                        SensorDataUpdated::dispatch($deviceId, $jsonDoc['category'], $jsonDoc['value']);
                        $updateSensor = SensorData::where([['deviceId', $deviceId], ['category', $jsonDoc['category']]])->value('data');
                        // echo "data = " .$updateSensor;
                        if($updateSensor === null){
                            SensorData::create([
                                'deviceId' => $deviceId,
                                'category' => $jsonDoc['category'],
                                'data' => $jsonDoc['value']
                            ]);
                        } else{
                            SensorData::where([['deviceId', $deviceId], ['category', $jsonDoc['category']]])->update(['data' => $jsonDoc['value']]);
                        }
                    }
                }
            } else{
                echo 'error';
            }
        } else if($topic == 'iotsmartkos/setup'){
            $deviceId = IotDevice::where('token', $jsonDoc['token'])->value('id');
            $roomId = IotDevice::where('id', $deviceId)->value('roomId');
            $userId = Room::where('id', $roomId)->value('userId');

            for($i=1; $i<=6; $i++){
                $relayStatus[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('status');
                $relayCategory[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('categoryId');
                if($relayCategory[$i] == 2){
                    $threshold[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('tempThreshold');
                } else{
                    $threshold[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('ldrThreshold');
                }
                $relayAutomation[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('automation');
                $relayPir[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('pirAuto');
                $relayOnHour[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('on_hour');
                $relayOffHour[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('off_hour');
                $relayOnMinute[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('on_minute');
                $relayOffMinute[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('off_minute');
                $relayturnedOn[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('turnedOn');
                $relayturnedOff[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('turnedOff');
            }
            $timezone = Room::where('id', $roomId)->value('timezone');
            $maxPower = Room::where('id', $roomId)->value('power');
            $pirStatus = SensorData::where([['deviceId', $deviceId], ['category', 'pir']])->value('data');
            $lastHour = KwhHistory::where([['userId', $userId], ['roomId', $roomId]])->orderBy('id', 'desc')->value('hour');
            $lastDay = KwhHistory::where([['userId', $userId], ['roomId', $roomId]])->orderBy('id', 'desc')->value('day');
            $lastMonth = KwhHistory::where([['userId', $userId], ['roomId', $roomId]])->orderBy('id', 'desc')->value('month');
            $lastYear = KwhHistory::where([['userId', $userId], ['roomId', $roomId]])->orderBy('id', 'desc')->value('year');
            $pirOnHour = RoomBill::where('roomId', $roomId)->value('pirOnHour');
            $pirOnMin = RoomBill::where('roomId', $roomId)->value('pirOnMin');
            $pirOffHour = RoomBill::where('roomId', $roomId)->value('pirOffHour');
            $pirOffMin = RoomBill::where('roomId', $roomId)->value('pirOffMin');
            $pirInterval = RoomBill::where('roomId', $roomId)->value('pirInterval');
            $pirSchedule = RoomBill::where('roomId', $roomId)->value('pirSchedule');

            return $this->mqtt->publish(
                $jsonDoc['token'],  // Topic
                json_encode(array(                  // Message
                    'channel'       => 0,
                    'timezone'      => $timezone,
                    'lastHour'      => $lastHour,
                    'pirStatus'     => $pirStatus,
                    'lastDay'       => $lastDay,
                    'lastMonth'     => $lastMonth,
                    'lastYear'      => $lastYear,
                    'maxPower'      => $maxPower,
                    'pirOnHour'     => $pirOnHour,
                    'pirOnMin'      => $pirOnMin,
                    'pirOffHour'    => $pirOffHour,
                    'pirOffMin'     => $pirOffMin,
                    'pirInterval'   => $pirInterval,
                    'pirSchedule'   => $pirSchedule,
                    'status'        => [$relayStatus[1],        $relayStatus[2],        $relayStatus[3], $relayStatus[4], $relayStatus[5], $relayStatus[6]],
                    'category'      => [$relayCategory[1],      $relayCategory[2],      $relayCategory[3], $relayCategory[4], $relayCategory[5], $relayCategory[6]],
                    'threshold'     => [$threshold[1],          $threshold[2],          $threshold[3], $threshold[4], $threshold[5], $threshold[6]],
                    'automation'    => [$relayAutomation[1],    $relayAutomation[2],    $relayAutomation[3], $relayAutomation[4], $relayAutomation[5], $relayAutomation[6]],
                    'onHour'        => [$relayOnHour[1],        $relayOnHour[2],        $relayOnHour[3], $relayOnHour[4], $relayOnHour[5], $relayOnHour[6]],
                    'offHour'       => [$relayOffHour[1],       $relayOffHour[2],       $relayOffHour[3], $relayOffHour[4], $relayOffHour[5], $relayOffHour[6]],
                    'onMinute'      => [$relayOnMinute[1],      $relayOnMinute[2],      $relayOnMinute[3], $relayOnMinute[4], $relayOnMinute[5], $relayOnMinute[6]],
                    'offMinute'     => [$relayOffMinute[1],     $relayOffMinute[2],     $relayOffMinute[3], $relayOffMinute[4], $relayOffMinute[5], $relayOffMinute[6]],
                    'pirAuto'       => [$relayPir[1],           $relayPir[2],           $relayPir[3], $relayPir[4], $relayPir[5], $relayPir[6]],
                    'turnedOn'      => [$relayturnedOn[1],      $relayturnedOn[2],      $relayturnedOn[3], $relayturnedOn[4], $relayturnedOn[5], $relayturnedOn[6]],
                    'turnedOff'     => [$relayturnedOff[1],     $relayturnedOff[2],     $relayturnedOff[3], $relayturnedOff[4], $relayturnedOff[5], $relayturnedOff[6]]
                )),
                1,                                  // QoS Level
                true                                // Retain
            );
        }
    }

    /**
     * Execute the console command.
     */
     public function handle()
    {
        $this->mqtt = MQTT::connection();

        $this->mqtt->subscribe(
            'iotsmartkos/relay', // Topic
            function(string $topic, string $message) { // Callback
                $this->updateData($topic, $message);
                echo sprintf('Topic: %s', $topic);
                echo PHP_EOL;
                echo sprintf('Message: %s', $message);
                echo PHP_EOL;
            },
            1 // QoS
        );

        $this->mqtt->subscribe(
            'iotsmartkos/sensor',                       //Topic
            function(string $topic, string $message) {  // Callback
                $this->updateData($topic, $message);
                echo sprintf('Topic: %s', $topic);
                echo PHP_EOL;
                echo sprintf('Message: %s', $message);
                echo PHP_EOL;
            },
            1                                           // QoS
        );

        $this->mqtt->subscribe(
            'iotsmartkos/setup',                       //Topic
            function(string $topic, string $message) {  // Callback
                $this->updateData($topic, $message);
                echo sprintf('Topic: %s', $topic);
                echo PHP_EOL;
                echo sprintf('Message: %s', $message);
                echo PHP_EOL;
            },
            1                                        // QoS
        );

        $this->mqtt->loop();

        return "SUCCESS";
    }
}
