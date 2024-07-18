<?php

namespace App\Livewire;

use App\Models\IotDevice;
use App\Models\Relay;
use App\Models\Room;
use App\Models\RoomBill;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use PhpMqtt\Client\Facades\MQTT;

class Automation extends Component
{
    public $editPir = false;
    public $pirOrigin;
    public $pirInterval;
    public $pirIntervalInput;
    public $pirOnHour;
    public $pirOnMinute;
    public $pirOffHour;
    public $pirOff;
    public $pirOn;
    public $pirOffMinute;
    public $deviceToken;
    public $deviceId;
    public $label;
    public $status;
    public $automation;
    public $pirAuto;
    public $type;
    public $onHour;
    public $onHourInput;
    public $onMinuteInput;
    public $offHourInput;
    public $offMinuteInput;
    public $pirOnHourInput;
    public $pirOnMinInput;
    public $pirOffHourInput;
    public $pirOffMinInput;
    public $onMinute;
    public $offHour;
    public $offMinute;
    public $onTime;
    public $offTime;
    public $roomId;
    public $timezone;
    public $ldrThreshold;
    public $ldrThresholdInput;
    public $tempThreshold;
    public $tempThresholdInput;
    public $selectedRelay = null;
    public $selectedLabel = null;
    public $selectedType = null;
    public $userId;
    public $turnedOn;
    public $turnedOff;
    public $deviceConnected = false;

    public function render()
    {
        $this->userId = Auth::user()->id;
        $this->timezone = Room::where('userId', $this->userId)->value('timezone');
        $this->roomId = User::find($this->userId)->room->id;
        $this->deviceId = IotDevice::where('roomId', $this->roomId)->value('id');
        $this->deviceToken = IotDevice::where('roomId', $this->roomId)->value('token');
        $relayData = Relay::where('deviceId', $this->deviceId)->orderBy('number', 'asc')->get();
        if(count($relayData)){
            $this->deviceConnected = true;
            foreach($relayData as $data){
                $this->label[] = $data->label;
                $this->status[] = $data->status;
                $this->pirAuto[] = $data->pirAuto;
                $this->automation[] = $data->automation;
                $this->type[] = $data->categoryId;
                $this->onHour[] = $data->on_hour;
                $this->onMinute[] = $data->on_minute;
                $this->offHour[] = $data->off_hour;
                $this->offMinute[] = $data->off_minute;
                $this->ldrThreshold[] = $data->ldrThreshold;
                $this->tempThreshold[] = $data->tempThreshold;
                $this->turnedOn[] = $data->turnedOn;
                $this->turnedOff[] = $data->turnedOff;
            }

            for($i=0;$i<6;$i++){
                if($this->onHour[$i] !== null || $this->onMinute[$i] !== null){
                    $onTime = date_create($this->onHour[$i] .':'. $this->onMinute[$i]);
                    $this->onTime[$i] = date_format($onTime, 'H:i');
                }else{
                    $this->onTime[$i] = 0;
                }
                if($this->offHour[$i] !== null || $this->offMinute[$i] !== null){
                    $offTime = date_create($this->offHour[$i] .':'. $this->offMinute[$i]);
                    $this->offTime[$i] = date_format($offTime, 'H:i');
                }else{
                    $this->offTime[$i] = 0;
                }
            }

            $pirData = RoomBill::where([['userId', $this->userId], ['roomId', $this->roomId]])->get();
            foreach($pirData as $data){
                $this->pirOrigin = $data->pirSchedule;
                $this->pirInterval = $data->pirInterval;
                $this->pirOnHour = $data->pirOnHour;
                $this->pirOnMinute = $data->pirOnMin;
                $this->pirOffHour = $data->pirOffHour;
                $this->pirOffMinute = $data->pirOffMin;
                if($data->pirOnHour !== null || $data->pirOnMin !== null){
                    $pirOn = date_create($data->pirOnHour .':'. $data->pirOnMin);
                    $this->pirOn = date_format($pirOn, 'H:i');
                } else{
                    $this->pirOn = 0;
                }
                if($data->pirOffHour !== null || $data->pirOffMin !== null){
                    $pirOff = date_create($data->pirOffHour .':'. $data->pirOffMin);
                    $this->pirOff = date_format($pirOff, 'H:i');
                } else{
                    $this->pirOff = 0;
                }
            }
        }
        return view('livewire.automation');
    }

    public function editAutomation($number){
        $mqtt = MQTT::connection();
        switch($this->selectedType){
            case 1:
                $inputData = [
                    'selectedRelay' => 'required',
                    'selectedLabel' => 'required',
                    'selectedType' => 'required',
                    'onHourInput' => 'required',
                    'onMinuteInput' => 'required',
                    'offHourInput' => 'required',
                    'offMinuteInput' => 'required',
                ];
                $message = [
                    'selectedRelay.required' => 'Silakan pilih relay',
                    'selectedLabel.required' => 'Silakan isi nama perangkat',
                    'selectedType.required' => 'Silakan pilih tipe automasi',
                    'onHourInput.required' => 'Silakan masukkan jam ON',
                    'onMinuteInput.required' => 'Silakan masukkan menit ON',
                    'offHourInput.required' => 'Silakan masukkan jam OFF',
                    'offMinuteInput.required' => 'Silakan masukkan menit OFF',
                ];
                $validated = $this->validate($inputData, $message);
                $mqtt->publish(
                    $this->deviceToken,  // Topic
                    json_encode(array(                  // Message
                        'channel' => 2,
                        'number' => (int)$validated['selectedRelay'],
                        'category' => (int)$validated['selectedType'],
                        'onHour' => (int)$validated['onHourInput'],
                        'onMin' => (int)$validated['onMinuteInput'],
                        'offHour' => (int)$validated['offHourInput'],
                        'offMin' => (int)$validated['offMinuteInput'],
                    )),
                    1,                                  // QoS Level
                    false                                // Retain
                );
                Relay::where([['number', $validated['selectedRelay']], ['deviceId', $this->deviceId]])->update([
                    'label' => $validated['selectedLabel'],
                    'categoryId' => $validated['selectedType'],
                    'on_hour' => $validated['onHourInput'],
                    'on_minute' => $validated['onMinuteInput'],
                    'off_hour' => $validated['offHourInput'],
                    'off_minute' => $validated['offMinuteInput'],
                ]);

                $this->label[$number - 1] = $validated['selectedLabel'];
                $this->type[$number - 1] = $validated['selectedType'];
                $this->onHour[$number - 1] = $validated['onHourInput'];
                $this->onMinute[$number - 1] = $validated['onMinuteInput'];
                $onHour = date_create($this->onHour[$number - 1] .':'. $this->onMinute[$number - 1]);
                $this->onTime[$number - 1] = date_format($onHour, 'H:i');
                $this->offHour[$number - 1] = $validated['offHourInput'];
                $this->offMinute[$number - 1] = $validated['offMinuteInput'];
                $offHour = date_create($this->offHour[$number - 1] .':'. $this->offMinute[$number - 1]);
                $this->offTime[$number - 1] = date_format($offHour, 'H:i');
                break;
            case 2:
                $inputData = [
                    'selectedRelay' => 'required',
                    'selectedLabel' => 'required',
                    'selectedType' => 'required',
                    'tempThresholdInput' => 'required',
                ];
                $message = [
                    'selectedRelay.required' => 'Silakan pilih relay',
                    'selectedLabel.required' => 'Silakan masukkan nama perangkat',
                    'selectedType.required' => 'Silakan pilih tipe automasi',
                    'tempThresholdInput.required' => 'Silakan masukkan batas suhu',
                ];
                $validated = $this->validate($inputData, $message);
                $mqtt->publish(
                    $this->deviceToken,  // Topic
                    json_encode(array(                  // Message
                        'channel' => 2,
                        'number' => $validated['selectedRelay'],
                        'category' => $validated['selectedType'],
                        'threshold' => $validated['tempThresholdInput'],
                    )),
                    1,                                  // QoS Level
                    false                                // Retain
                );
                Relay::where([['number', $validated['selectedRelay']], ['deviceId', $this->deviceId]])->update([
                    'label' => $validated['selectedLabel'],
                    'categoryId' => $validated['selectedType'],
                    'tempThreshold' => $validated['tempThresholdInput'],
                ]);

                $this->label[$number - 1] = $validated['selectedLabel'];
                $this->type[$number - 1] = $validated['selectedType'];
                $this->tempThreshold[$number - 1] = $validated['tempThresholdInput'];
                break;
            case 3:
                $inputData = [
                    'selectedRelay' => 'required',
                    'selectedLabel' => 'required',
                    'selectedType' => 'required',
                    'ldrThresholdInput' => 'required',
                ];
                $message = [
                    'selectedRelay.required' => 'Silakan pilih relay',
                    'selectedLabel.required' => 'Silakan masukkan nama perangkat',
                    'selectedType.required' => 'Silakan pilih tipe automasi',
                    'ldrThresholdInput.required' => 'Silakan masukkan batas cahaya',
                ];
                $validated = $this->validate($inputData, $message);
                $mqtt->publish(
                    $this->deviceToken,  // Topic
                    json_encode(array(                  // Message
                        'channel' => 2,
                        'number' => $validated['selectedRelay'],
                        'category' => $validated['selectedType'],
                        'threshold' => $validated['ldrThresholdInput'],
                    )),
                    1,                                  // QoS Level
                    false                                // Retain
                );
                Relay::where([['number', $validated['selectedRelay']], ['deviceId', $this->deviceId]])->update([
                    'label' => $validated['selectedLabel'],
                    'categoryId' => $validated['selectedType'],
                    'ldrThreshold' => $validated['ldrThresholdInput'],
                ]);
                $this->label[$number - 1] = $validated['selectedLabel'];
                $this->type[$number - 1] = $validated['selectedType'];
                $this->ldrThreshold[$number - 1] = $validated['ldrThresholdInput'];
                break;
            default:
                break;
        }
        $mqtt->loop(true, true);
        $this->selectedRelay = null;
        $this->selectedType = null;
        $this->selectedLabel = null;
        session()->flash('message', 'Berhasil update data relay ' .$number);
    }

    public function selectType($type){
        $this->selectedType = $type;
    }

    public function selectRelay($number){
        $this->selectedRelay = $number+1;
        if($this->type[$number] === null){
            $this->selectedType = 1;
        } else{
            $this->selectedType = $this->type[$number];
        }
        $this->selectedLabel = $this->label[$number];
        $this->ldrThresholdInput = $this->ldrThreshold[$number];
        $this->tempThresholdInput = $this->tempThreshold[$number];
        $this->onHourInput = $this->onHour[$number];
        $this->onMinuteInput = $this->onMinute[$number];
        $this->offHourInput = $this->offHour[$number];
        $this->offMinuteInput = $this->offMinute[$number];
    }

    public function editingPir($value){
        $this->editPir = $value;
        $this->pirOnHourInput = $this->pirOnHour;
        $this->pirOnMinInput = $this->pirOnMinute;
        $this->pirOffHourInput = $this->pirOffHour;
        $this->pirOffMinInput = $this->pirOffMinute;
        $this->pirIntervalInput = $this->pirInterval;
    }

    public function selectPir(){
        if($this->pirOnMinute !== null){
            $mqtt = MQTT::connection();
            $this->pirOrigin = !$this->pirOrigin;
            $mqtt->publish(
                $this->deviceToken,  // Topic
                json_encode(array(                  // Message
                    'channel' => 4,
                    'status' => $this->pirOrigin,
                    'interval' => $this->pirInterval,
                    'onHour' => $this->pirOnHour,
                    'onMin' => $this->pirOnMinute,
                    'offHour' => $this->pirOffHour,
                    'offMin' => $this->pirOffMinute,
                )),
                1,                                  // QoS Level
                false                                // Retain
            );
            $mqtt->loop(true, true);
            RoomBill::where([['userId', $this->userId], ['roomId', $this->roomId]])->update([
                'pirSchedule' => $this->pirOrigin
            ]);
        } else{
            session()->flash('alert', 'Setting waktu terlebih dahulu');
        }
    }

    public function storePir(){
        $mqtt = MQTT::connection();
        $inputData = [
            'pirIntervalInput' => 'required|integer|between:0,59',
            'pirOnHourInput' => 'required|integer|between:0,23',
            'pirOnMinInput' => 'required|integer|between:0,59',
            'pirOffHourInput' => 'required|integer|between:0,23',
            'pirOffMinInput' => 'required|integer|between:0,59',
        ];
        $validated = $this->validate($inputData);
        $mqtt->publish(
            $this->deviceToken,  // Topic
            json_encode(array(                  // Message
                'channel' => 4,
                'status' => (bool)$this->pirOrigin,
                'interval' => (int)$validated['pirIntervalInput'],
                'onHour' => (int)$validated['pirOnHourInput'],
                'onMin' => (int)$validated['pirOnMinInput'],
                'offHour' => (int)$validated['pirOffHourInput'],
                'offMin' => (int)$validated['pirOffMinInput'],
            )),
            1,                                  // QoS Level
            false                                // Retain
        );
        RoomBill::where([['userId', $this->userId], ['roomId', $this->roomId]])->update([
            'pirInterval' => (int)$validated['pirIntervalInput'],
            'pirOnHour' => (int)$validated['pirOnHourInput'],
            'pirOnMin' => (int)$validated['pirOnMinInput'],
            'pirOffHour' => (int)$validated['pirOffHourInput'],
            'pirOffMin' => (int)$validated['pirOffMinInput'],
        ]);
        $mqtt->loop(true, true);
        session()->flash('message', 'Berhasil update data PIR');
        $this->editingPir(0);
    }

    public function autoSwitch($relayNumber){
        $mqtt = MQTT::connection();
        $this->automation[$relayNumber] = !$this->automation[$relayNumber];
        $this->turnedOn[$relayNumber] = 0;
        $this->turnedOff[$relayNumber] = 0;
        $mqtt->publish(
            $this->deviceToken,  // Topic
            json_encode(array(                  // Message
                'channel' => 1,
                'number' => (int)$relayNumber + 1,
                'status' => (int)$this->status[$relayNumber],
                'automation' => (bool)$this->automation[$relayNumber],
                'turnedOn' => (bool)$this->turnedOn[$relayNumber],
                'turnedOff' => (bool)$this->turnedOff[$relayNumber],
            )),
            1,                                  // QoS Level
            false                                // Retain
        );
        $mqtt->loop(true, true);
        Relay::where([['deviceId', $this->deviceId], ['number', $relayNumber+1]])->update([
            'automation' => $this->automation[$relayNumber],
            'turnedOn' => $this->turnedOn[$relayNumber],
            'turnedOff' => $this->turnedOff[$relayNumber],
        ]);
    }

    public function pirSwitch($relayNumber){
        $mqtt = MQTT::connection();
        $this->pirAuto[$relayNumber] = !$this->pirAuto[$relayNumber];
        $mqtt->publish(
            $this->deviceToken,  // Topic
            json_encode(array(                  // Message
                'channel' => 3,
                'number' => (int)$relayNumber + 1,
                'pirAuto' => (bool)$this->pirAuto[$relayNumber],
            )),
            1,                                  // QoS Level
            false                                // Retain
        );
        $mqtt->loop(true, true);
        Relay::where([['deviceId', $this->deviceId], ['number', $relayNumber+1]])->update([
            'pirAuto' => $this->pirAuto[$relayNumber]
        ]);
    }
}
