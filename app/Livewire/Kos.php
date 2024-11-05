<?php

namespace App\Livewire;

use App\Models\IotDevice;
use App\Models\Kos as ModelsKos;
use App\Models\Relay;
use App\Models\Room;
use App\Models\RoomBill;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use PhpMqtt\Client\Facades\MQTT;

class Kos extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $name;
    public $address;
    public $wifiSsid;
    public $wifiPass;
    public $adminId;
    public $kosId;
    public $roomId;
    public $token;
    public $isAdding = false;
    public $isEditing = false;
    public $seeRoom = false;
    public $addRoom = false;
    public $editRoom = false;
    public $keyword;
    public $selectedData = [];
    public $kosName;
    public $roomData;
    public $power;
    public $roomUser;
    public $roomDevice;
    public $tariff;
    // public $selectedTimezone = 7;
    public $timezone = 7;
    // public $selectedRoomName;
    // public $selectedRoomPower;
    // public $selectedRoomTimezone;

    public function render()
    {
        $paginate = 5;
        if($this->keyword != null){
            $kosData = ModelsKos::where([['adminId', Auth::user()->id], ['name', 'like', '%'.$this->keyword.'%']])
            ->orWhere([['adminId', Auth::user()->id], ['address', 'like', '%'.$this->keyword.'%']])
            ->orderBy('name', 'asc')->paginate($paginate);
        } else{
            $kosData = ModelsKos::where('adminId', Auth::user()->id)->orderBy('name', 'asc')->paginate($paginate);
        }

        // foreach($kosData as $data => $value){
        //     $this->isEditing[$value->id] = false;
        // }
        // dd($this->isEditing);
        return view('livewire.kos', ['kosData' => $kosData]);
    }

    public function addKos($value){
        // dd($this->kosId);

        $this->isAdding = $value;
        $this->isEditing = false;
    }

    public function store(){
        // dd($this->kosId);

        if($this->seeRoom == false){
            $this->adminId = Auth::user()->id;
            $inputData = [
                'name' => 'required',
                'address' => 'required',
                'adminId' => 'required|exists:users,id'
            ];

            $message =  [
                'name.required' => 'Nama Kos wajib diisi',
                'address.required' => 'Alamat Kos wajib diisi',
                'adminId.required.exists:users,id' => 'Akun Anda tidak terdaftar'
            ];

            $validated = $this->validate($inputData, $message);
            ModelsKos::create($validated);
        } else{
            $inputData = [
                'name' => 'required',
                'power' => 'required|int',
                'tariff' => 'required|int',
                'timezone' => 'required',
                'kosId' => 'required|exists:kos,id',
            ];

            $message =  [
                'name.required' => 'Nama Kos wajib diisi',
                'power.required' => 'Daya Kos wajib diisi',
                'tariff.required' => 'Tarif Kos wajib diisi',
                'timezone.required' => 'Timezone Kos wajib diisi',
                'kosId.required.exists:kos,id' => 'Akun Anda tidak terdaftar'
            ];
            $validated = $this->validate($inputData, $message);
            Room::create($validated);
            $this->getRoom($this->kosId, true);
        }
        session()->flash('message', 'Berhasil Input Data');
        $this->clearInput();
    }

    public function edit($id){
        $this->isEditing = true;
        $this->isAdding = false;
        if($this->seeRoom == false){
            $searchData = ModelsKos::find($id);
            // dd($searchData);
            $this->name = $searchData->name;
            $this->address = $searchData->address;
            $this->kosId = $id;
        } else{
            $this->roomId = $id;
            $searchData = Room::find($id);
            // dd($searchData);
            $this->name = $searchData->name;
            $this->power = $searchData->power;
            $this->tariff = $searchData->tariff;
            $this->timezone = $searchData->timezone;
            // $this->kosId = $id;
        }
    }
    public function selectTimezone($value){
        $this->timezone = $value;
    }

    public function update(){
        if($this->seeRoom == false){
            $this->adminId = Auth::user()->id;
            $inputData = [
                'name' => 'required',
                'address' => 'required',
            ];

            $message =  [
                'name.required' => 'Nama Kos wajib diisi',
                'address.required' => 'Alamat Kos wajib diisi',
            ];

            $validated = $this->validate($inputData, $message);
            $searchData = ModelsKos::find($this->kosId);
        } else{
            $inputData = [
                'name' => 'required',
                'power' => 'required|int',
                'tariff' => 'required|int',
                'timezone' => 'required',
                'kosId' => 'required|exists:kos,id',
            ];

            $message =  [
                'name.required' => 'Nama Kos wajib diisi',
                'power.required' => 'Daya Kos wajib diisi',
                'tariff.required' => 'Tarif Kos wajib diisi',
                'timezone.required' => 'Timezone Kos wajib diisi',
                'kosId.required.exists:kos,id' => 'Akun Anda tidak terdaftar'
            ];

            $validated = $this->validate($inputData, $message);
            $deviceToken = IotDevice::where('roomId', $this->roomId)->value('token');
            if($deviceToken){
                $mqtt = MQTT::connection();
                $mqtt->publish(
                    $deviceToken,  // Topic
                    json_encode(array(                  // Message
                        'channel' => 5,
                        'maxPower' => $validated['power']
                    )),
                    1,                                  // QoS Level
                    false                                // Retain
                );
                $mqtt->loop(true, true);
            }
            $searchData = Room::find($this->roomId);
            $this->getRoom($this->kosId, true);
        }
        // dd($validated['timezone']);

        $searchData->update($validated);
        session()->flash('message', 'Berhasil Update Data');
        $this->clearInput();
    }

    public function delete(){
        if($this->seeRoom == false){
            $user = [];
            $kosUser = Room::where('kosId', $this->kosId)->get('userId');
            foreach($kosUser as $userId => $value){
                if($value->userId !== null){
                    $user[] = $value->userId;
                }
            }
            if($user){
                // dd("Berpenghuni");
                session()->flash('error', 'Tidak dapat menghapus Kos berpenghuni');
            } else{
                // dd("Kosong");
                ModelsKos::find($this->kosId)->delete();
                session()->flash('message', 'Berhasil Hapus Data');
            }
        } else{
            if(Room::where('id', $this->roomId)->value('userId') === null){
                Room::find($this->roomId)->delete();
                session()->flash('message', 'Berhasil Hapus Data');
            } else{
                session()->flash('error', 'Tidak dapat menghapus Kamar berpenghuni');
            }
            $this->getRoom($this->kosId, true);
        }
        $this->clearInput();
    }

    public function confirmDelete($id){
        // dd($id);
        if($this->seeRoom == false){
            if($id != 0){
                $this->kosId = $id;
            }
        } else{
            $this->roomId = $id;
        }
    }

    public function getRoom($kos, $value){
        $this->clearInput();
        $this->isAdding = false;
        $this->isEditing = false;
        $this->kosId = $kos;
        $this->seeRoom = $value;
        if($value == true){
            if($this->roomUser){
                array_splice($this->roomUser, 0, count($this->roomUser));
            }
            if($this->roomDevice){
                array_splice($this->roomDevice, 0, count($this->roomDevice));
            }

            $this->kosName = ModelsKos::where('id', $kos)->value('name');
            $this->roomData = Room::where('kosId', $kos)->get();
            if($this->roomData->value('name')){
                foreach($this->roomData as $room => $value){
                    $this->roomUser[] = User::where('id', $value->userId)->value('name');
                    $this->roomDevice[] = IotDevice::where('roomId', $value->id)->value('token');
                }
            } else{
                $this->roomData = null;
            }
        }
        // dd($this->roomDevice);
    }

    public function createDevice($roomId){
        $this->roomId = $roomId;

        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz$_';

        $this->token = substr(str_shuffle($str_result), 0, 40);
        // $this->token = 'o9OkHbUpRhg7ltIGTwcWQzjLEyNfYSCFnZAi1q2D';

        $validated = $this->validate([
            'roomId' => 'required',
            'token' => 'required|unique:iot_device,token',
        ],[
            'token.unique' => 'Error, silakan ulangi lagi'
        ]);
        // dd($validated);
        IotDevice::create($validated);
        $deviceId = IotDevice::where('roomId', $this->roomId)->value('id');
        for($i=1 ; $i<=6; $i++){
            Relay::create([
                'number' => $i,
                'deviceId' => $deviceId,
            ]);
        }

        $this->clearInput();
        $this->getRoom($this->kosId, true);
        session()->flash('message', 'Berhasil menautkan perangkat');
    }

    public function setRoomId($room,
        // $wifiSsid, $wifiPass
    ){
        $this->roomId = $room;
        // $this->wifiSsid = $wifiSsid;
        // $this->wifiPass = $wifiPass;
    }

    public function donwloadSketch(){
        // Storage::disk('local')->put('example.txt', 'Contents');
        $path = Storage::path('body.ino');
        // dd($path);
        $body = File::get($path);

        $token = IotDevice::where('roomId', $this->roomId)->value('token');
        $writeToken = 'const char token[] PROGMEM = "' .$token. '";';

        $wifiSsid = 'const char ssid[] PROGMEM = "'. $this->wifiSsid. '";';
        $wifiPass = 'const char password[] PROGMEM = "' .$this->wifiPass. '";';

        // dd($writeToken, $writeMqtt);

        Storage::copy('head.ino', 'public\complete-'.$this->roomId.'.ino');
        Storage::append('public\complete-'.$this->roomId.'.ino', $wifiSsid);
        Storage::append('public\complete-'.$this->roomId.'.ino', $wifiPass);
        Storage::append('public\complete-'.$this->roomId.'.ino', $writeToken);
        Storage::append('public\complete-'.$this->roomId.'.ino', $body);

        // Storage::delete('public\complete-'.$this->roomId.'.ino');

        return Storage::download('public\complete-'.$this->roomId.'.ino', 'sketch.ino');
    }

    public function deleteUser(){
        Room::where('id', $this->roomId)->update(['userId' => null]);
        RoomBill::where('roomId', $this->roomId)->delete();
        $this->clearInput();
        $this->render();
        session()->flash('message', 'Berhasil hapus penghuni');
    }

    public function clearInput(){
        $this->name = '';
        $this->address = '';
        $this->adminId = 0;
        $this->roomId = 0;
        $this->name = '';
        $this->power = '';
        $this->tariff = '';
        $this->timezone = 7;
        // $this->kosId = 0;
        $this->isAdding = false;
        $this->isEditing = false;
        $this->selectedData = [];
        $this->seeRoom = false;
        $this->addRoom = false;
        $this->editRoom = false;
        // $this->selectedTimezone = 7;
    }
}
