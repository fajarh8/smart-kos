<?php

namespace App\Livewire;

use App\Models\Kos;
use App\Models\Room;
use App\Models\RoomBill;
use App\Models\User;
use App\Models\UserRequest as ModelsUserRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserRequest extends Component
{
    public $kosData;
    public $roomData;
    public $userId;
    public $roomId;
    public $userRequest;
    public $selectedKos;
    public $viewRoom = false;

    public function render()
    {
        $adminId = Auth::user()->id;
        $this->kosData = [];
        $this->roomData = [];
        $this->userRequest = [];

        foreach(Kos::where('adminId', $adminId)->orderBy('name', 'asc')->get(['id', 'name', 'address']) as $kos => $data){
            // $this->kosData[$kos]['id'] = $data->id;
            $this->kosData[$kos]['name'] = $data->name;
            $this->kosData[$kos]['address'] = $data->address;
            $this->kosData[$kos]['count'] = 0;

            foreach (Room::where('kosId', $data->id)->orderBy('name', 'asc')->get(['id', 'name', 'power', 'tariff']) as $room => $value) {
                $this->roomData[$kos][$room]['id'] = $value->id;
                $this->roomData[$kos][$room]['name'] = $value->name;
                $this->roomData[$kos][$room]['power'] = $value->power;
                $this->roomData[$kos][$room]['tariff'] = $value->tariff;
                $requestData = ModelsUserRequest::where([['roomId', $value->id], ['isVerified', 0]])->orderBy('roomId', 'asc')->get(['userId', 'isVerified']);
                // dd($requestData);
                if(count($requestData)){
                    foreach($requestData as $index => $request){
                        $this->userRequest[$kos][$room][$index]['userId'] = $request->userId;
                        $this->userRequest[$kos][$room][$index]['userName'] = User::where('id', $request->userId)->value('name');
                        $this->userRequest[$kos][$room][$index]['roomName'] = $value->name;
                        $this->userRequest[$kos][$room][$index]['kosName'] = $data->name;
                        $this->userRequest[$kos][$room][$index]['verified'] = $request->isVerified;
                        $this->kosData[$kos]['count']++;
                    }
                } else{
                    $this->userRequest[$kos][$room] = [];
                }
            }
        }
        // dd($this->userRequest);
        return view('livewire.user-request');
    }

    public function viewRequest($kos){
        $this->selectedKos = $kos;
        // dd($this->roomData[$kos]);
        $this->viewRoom = !$this->viewRoom;
    }

    public function verifyRequest($userId, $roomId){
        // dd($roomId, $userId);
        $this->userId = $userId;
        $this->roomId = $roomId;
    }

    public function confirmAction($status){
        ModelsUserRequest::where('userId', $this->userId)->update(['isVerified' => $status]);
        $this->viewRoom = false;
        $this->render();

        if($status == true){
            Room::where('id', $this->roomId)->update(['userId' => $this->userId]);
            RoomBill::create([
                'roomId' => $this->roomId,
                'userId' => $this->userId
            ]);
            session()->flash('success', 'Berhasil menerima permintaan');
        } else{
            session()->flash('success', 'Berhasil menolak permintaan');
        }
    }
}
