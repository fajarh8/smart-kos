<?php

namespace App\Livewire;

use App\Models\IotDevice;
use App\Models\KwhHistory;
use App\Models\Relay;
use App\Models\Room;
use App\Models\RoomBill;
use App\Models\SensorData as ModelsSensorData;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use PhpMqtt\Client\Facades\MQTT;

class SensorData extends Component
{
    public $deviceId;
    public $userId;
    public $deviceToken,
        $roomId,
        $roomBill,
        $chartData,
        $electricAccess,
        $maxPower,
        $temperature,
        $humidity,
        $ldr,
        $pir,
        $energy,
        $apparentPower,
        $relayStatus,
        $relayTurnedOn,
        $relayTurnedOff,
        $relayLabel,
        $relayPir,
        $relayId,
        $relayAuto;

    public function render()
    {
        $this->userId = Auth::user()->id;

        foreach(Room::where('userId', $this->userId)->get() as $roomData){
            $this->roomId = $roomData->id;
            $this->maxPower = $roomData->power;
            $gmt = $roomData->timezone;
        }

        if($gmt == 7){
            $timezone = 'Asia/Jakarta';
        } else if($gmt == 8){
            $timezone = 'Asia/Makassar';
        } else if($gmt == 9){
            $timezone = 'Asia/Jayapura';
        }
        date_default_timezone_set($timezone);
        $date = idate('d');
        $month = idate('m');
        $year = idate('Y');
        $monthEnd = idate('t');

        $device = IotDevice::where('roomId', $this->roomId)->get();
        // dd($device);
        if(count($device)){
            foreach($device as $data => $deviceData){
                $this->deviceId = $deviceData->id;
                $this->deviceToken = $deviceData->token;
                $this->electricAccess = 1;
            }
            // dd($this->deviceId);

            // foreach(ModelsSensorData::where([['deviceId', $this->deviceId]])->get() as $sensorData){
                $this->humidity = ModelsSensorData::where([['deviceId', $this->deviceId], ['category', 'humidity']])->value('data');
                $this->temperature = ModelsSensorData::where([['deviceId', $this->deviceId], ['category', 'temperature']])->value('data');
                $this->ldr = ModelsSensorData::where([['deviceId', $this->deviceId], ['category', 'ldr']])->value('data');
                $this->pir = ModelsSensorData::where([['deviceId', $this->deviceId], ['category', 'pir']])->value('data');
                $this->apparentPower = ModelsSensorData::where([['deviceId', $this->deviceId], ['category', 'pzem-apparentPower']])->value('data');
            // }

            // foreach(KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['month', $month], ['year', $year]])->orderBy('id', 'asc')->get() as $kwhData){
                $this->energy = KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['month', $month], ['year', $year], ['day', $monthEnd+1]])->latest()->value('kwh');
                $this->roomBill = KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['month', $month], ['year', $year], ['day', $monthEnd+1]])->latest()->value('bill');
                // $lastHour = KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['month', $month], ['year', $year], ['day', $date]])->latest()->value('hour');
                $lastHour = idate('H');
                // dd($lastHour);
            // }
// dd($this->energy);
            if($lastHour){
                for($i=0; $i<=$lastHour; $i++){
                    $hour = date_create($i.':0');
                    $this->chartData[$i] = [
                        'hour' => date_format($hour, 'H:i'),
                        'kwh' => null
                    ];
                }
                foreach(KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['day', $date], ['month', $month], ['year', $year]])->orderBy('id', 'asc')->get() as $kwhHistory){
                    $hour = date_create($kwhHistory->hour.':0');
                    // if($kwhHistory->hour == 0){

                    // } else{
                        $this->chartData[$kwhHistory->hour] = [
                            'hour' => date_format($hour, 'H:i'),
                            'kwh' => $kwhHistory->kwh
                            // 'kwh' => number_format($kwhHistory->kwh,6,",",".")
                        ];
                    // }
                }
                // dd($this->chartData);

                if($this->chartData === null){
                    $this->chartData[0] = [
                        'hour' => '00:00',
                        'kwh' => 0,
                    ];
                } else{
                    // if($date == 1){
                    //     $this->chartData[0] = [
                    //         'hour' => '00:00',
                    //         'kwh' => 0
                    //     ];
                    // }
                    for($i=0; $i<=$lastHour; $i++){
                        if($this->chartData[$i]['kwh'] === null){
                            if($i == 0){
                                $this->chartData[$i]['kwh'] = 0;
                            } else{
                                $this->chartData[$i]['kwh'] = $this->chartData[$i-1]['kwh'];
                            }
                        }
                    }
                }
                // dd($this->chartData);


                // $this->relayData = Relay::where('deviceId', $this->deviceId)->orderBy('number', 'asc')->get();
                if($lastHour == 24){
                    array_splice($this->chartData, 0, count($this->chartData));
                    // unset($this->chartData);
                    $this->chartData[0] = [
                        'hour' => '00:00',
                        'kwh' => 0,
                    ];
                }
            }
            foreach (Relay::where('deviceId', $this->deviceId)->orderBy('number', 'asc')->get() as $relayData) {
                $this->relayLabel[] = $relayData->label;
                $this->relayId[] = $relayData->id;
                $this->relayStatus[] = $relayData->status;
                $this->relayTurnedOn[] = $relayData->turnedOn;
                $this->relayTurnedOff[] = $relayData->turnedOff;
                $this->relayAuto[] = $relayData->automation;
                $this->relayPir[] = $relayData->pirAuto;
            };
        } else{
            $this->deviceId = 0;
            $this->electricAccess = 0;
        }
        // dd($this->chartData);

        // dd($this->chartData);
        // dd($this->relayStatus);
        // $this->voltage = IotDevice::find($this->deviceId)->sensor()->orderBy('updated_at', 'desc')->where('category', 'pzem-voltage')->value('data');
        // $this->current = IotDevice::find($this->deviceId)->sensor()->orderBy('updated_at', 'desc')->where('category', 'pzem-current')->value('data');
        // $this->power = IotDevice::find($this->deviceId)->sensor()->orderBy('updated_at', 'desc')->where('category', 'pzem-power')->value('data');
        // $this->freq = IotDevice::find($this->deviceId)->sensor()->orderBy('updated_at', 'desc')->where('category', 'pzem-freq')->value('data');
        // $this->powerFactor = IotDevice::find($this->deviceId)->sensor()->orderBy('updated_at', 'desc')->where('category', 'pzem-powerFactor')->value('data');
        // $this->activePower = IotDevice::find($this->deviceId)->sensor()->orderBy('updated_at', 'desc')->where('category', 'pzem-activePower')->value('data');
        // $this->reactivePower = IotDevice::find($this->deviceId)->sensor()->orderBy('updated_at', 'desc')->where('category', 'pzem-reactivePower')->value('data');

        return view('livewire.sensor-data');
    }

    #[On('echo-channel:data-updated.{deviceId},SensorDataUpdated')]
    public function onSensorDataUpdated($message)
    {
        switch ($message[0]) {
            case 'temperature':
                $this->temperature = $message[1];
                break;
            case 'humidity':
                $this->humidity = $message[1];
                break;
            case 'ldr':
                $this->ldr = $message[1];
                break;
            case 'pir':
                $this->pir = $message[1];
                break;
            case 'pzem-energy':
                $this->energy = $message[1];
                break;
            case 'pzem-apparentPower':
                $this->apparentPower = $message[1];
                break;
            default:
                # code...
                break;
        }
    }

    #[On('echo-channel:data-updated.{deviceId},RelayStatusUpdated')]
    public function onRelayStatusUpdated($message)
    {
        // dd($message);
        $this->relayStatus[$message[0]-1] = $message[1];
    }

    #[On('echo-channel:data-updated.{deviceId},RoomBillUpdated')]
    public function onRoomBillUpdated($message)
    {
        $this->render();
        // $count = count($this->chartData);
        // $this->chartData[$count] = [
        //     'hour' => date_format(date_create($message[0].':0'), 'H:i'),
        //     'kwh' => $message[1],
        // ];
        // $this->energy = KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['month', $month], ['year', $year], ['day', $date]])->orderBy('id', 'desc')->value('kwh');
        // $this->roomBill = KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['month', $month], ['year', $year], ['day', $monthEnd+1]])->orderBy('id', 'desc')->value('bill');
    }

    #[On('echo-channel:data-updated.{deviceId},PowerIsOver')]
    public function onPowerIsOver()
    {
        for($i=0;$i<=6;$i++){
            $this->relayStatus[$i] = 0;
        }
        session()->flash('danger', 'Listrik melebihi batas');
    }

    // public function editAutomation(){
    //     return view('livewire.automation');
    // }

    // public function getRoomBill(){
    //     $this->roomBill = User::find($this->userId)->roomBill->bill;
    //     $this->maxPower = User::find($this->userId)->room->power;
    //     $this->electricAccess = User::find($this->userId)->roomBill->electric_access;

    //     // sleep(10);
    //     $this->getRoomBill();
    // }

    public function automationSwitch($relayNumber){
        $this->relayAuto[$relayNumber] = !$this->relayAuto[$relayNumber];
        $this->relayTurnedOn[$relayNumber] = 0;
        $this->relayTurnedOff[$relayNumber] = 0;

        $mqtt = MQTT::connection();
        $mqtt->publish(
            $this->deviceToken,  // Topic
            json_encode(array(                  // Message
                'channel' => 1,
                'number' => $relayNumber + 1,
                'status' => (bool)$this->relayStatus[$relayNumber],
                'automation' => (bool)$this->relayAuto[$relayNumber],
                'turnedOn' => (bool)$this->relayTurnedOn[$relayNumber],
                'turnedOff' => (bool)$this->relayTurnedOff[$relayNumber],
            )),
            1,                                  // QoS Level
            false                                // Retain
        );
        $mqtt->loop(true, true);
        Relay::where('id', $this->relayId[$relayNumber])->update([
            'automation' => $this->relayAuto[$relayNumber],
            'turnedOn' => $this->relayTurnedOn[$relayNumber],
            'turnedOff' => $this->relayTurnedOff[$relayNumber]
        ]);
    }

    // public function random_strings($length_of_string){
    //     // String of all alphanumeric character
    //     $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$&_';

    //     // Shuffle the $str_result and returns substring
    //     // of specified length
    //     return substr(str_shuffle($str_result), 0, $length_of_string);
    // }

    public function powerSwitch($relayNumber, $pir){
        // // This function will generate
        // // Random string of length 10
        // dd($this->random_strings(40));
        if($pir == 1){
            if(($this->relayPir[$relayNumber] == 1 && $this->pir != 0) || ($this->relayPir[$relayNumber] == 0)){
                if($this->relayAuto[$relayNumber] == 1){
                    if($this->relayStatus[$relayNumber] == 1){
                        if($this->relayTurnedOff[$relayNumber] == 0){
                            $this->relayTurnedOff[$relayNumber] = 1;
                        } else{
                            $this->relayTurnedOff[$relayNumber] = 0;
                        }
                        $this->relayTurnedOn[$relayNumber] = 0;
                    } else{
                        if($this->relayTurnedOn[$relayNumber] == 0){
                            $this->relayTurnedOn[$relayNumber] = 1;
                        } else{
                            $this->relayTurnedOn[$relayNumber] = 0;
                        }
                        $this->relayTurnedOff[$relayNumber] = 0;
                    }
                }
                $this->relayStatus[$relayNumber] = !$this->relayStatus[$relayNumber];
            }
            $mqtt = MQTT::connection();
            $mqtt->publish(
                $this->deviceToken,  // Topic
                json_encode(array(                  // Message
                    'channel' => 1,
                    'number' => (int)$relayNumber + 1,
                    'status' => (bool)$this->relayStatus[$relayNumber],
                    'automation' => (bool)$this->relayAuto[$relayNumber],
                    'turnedOn' => (bool)$this->relayTurnedOn[$relayNumber],
                    'turnedOff' => (bool)$this->relayTurnedOff[$relayNumber],
                )),
                1,                                  // QoS Level
                false                                // Retain
            );
            $mqtt->loop(true, true);
            // dd("sended");
            Relay::where('id', $this->relayId[$relayNumber])->update([
                'status' => $this->relayStatus[$relayNumber],
                'turnedOn' => $this->relayTurnedOn[$relayNumber],
                'turnedOff' => $this->relayTurnedOff[$relayNumber],
            ]);
        } else{
            session()->flash('alert', 'Tidak dapat menghidupkan perangkat terkait ketika kamar kosong.');
        }
    }
}
