<?php

namespace App\Livewire;

use App\Models\Kos;
use App\Models\Room;
use App\Models\RoomBill;
use App\Models\User;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KosSearch extends Component
{
    public $emptyRoom;
    public $emptyKos;
    public $anyRequest = false;
    public $selectedKos;
    public $selectedRoom;
    public $roomIndex;
    public $requestData = [];
    public $roomDetail = false;
    public $getRoom = false;

    public function render()
    {
        $this->emptyKos = [];
        $this->emptyRoom = [];
        $this->emptyKos['id'] = [];
        $this->requestData = [];
        $this->requestData['isVerified'] = 0;

        $value = UserRequest::where('userId', Auth::user()->id)->get(['roomId', 'isVerified']);
        if(count($value)){
            $this->anyRequest = true;
            $this->requestData['roomId'] = $value->value('roomId');
            $this->requestData['roomName'] = Room::where('id', $value->value('roomId'))->value('name');
            $this->requestData['isVerified'] = $value->value('isVerified');
            $this->requestData['power'] = Room::where('id', $value->value('roomId'))->value('power');
            $this->requestData['tariff'] = Room::where('id', $value->value('roomId'))->value('tariff');

            $kosId = Room::where('id', $value->value('roomId'))->value('kosId');
            $this->requestData['kosName'] = Kos::where('id', $kosId)->value('name');
            $this->requestData['kosAddress'] = Kos::where('id', $kosId)->value('address');

            $adminId = Kos::where('id', $kosId)->value('adminId');
            $this->requestData['adminName'] = User::where('id', $adminId)->value('name');
        }

        foreach(Room::whereNull('userId')->orderBy('name', 'asc')->get(['id', 'name', 'kosId', 'tariff', 'power']) as $room => $data){
            $this->emptyRoom[$room]['id'] = $data->id;
            $this->emptyRoom[$room]['kosId'] = $data->kosId;
            $this->emptyRoom[$room]['name'] = $data->name;
            $this->emptyRoom[$room]['tariff'] = $data->tariff;
            $this->emptyRoom[$room]['power'] = $data->power;

            if(!in_array($data->kosId, $this->emptyKos['id'])){
                $this->emptyKos['id'][] = $data->kosId;
                $this->emptyKos['name'][] = Kos::where('id', $data->kosId)->value('name');
                $this->emptyKos['address'][] = Kos::where('id', $data->kosId)->value('address');
                $adminId = Kos::where('id', $data->kosId)->value('adminId');
                // $this->emptyKos['adminId'][] = $adminId;
                $this->emptyKos['adminName'][] = User::where('id', $adminId)->value('name');
                $this->emptyKos['empty'][] = 1;
            } else{
                $this->emptyKos['empty'][array_search($data->kosId, $this->emptyKos['id'])]++;
            }
        }
        // dd($this->emptyRoom);
        // dd($this->emptyKos, $this->emptyRoom);
        return view('livewire.kos-search');
    }

    public function requestDetail(){
        $this->roomDetail = !$this->roomDetail;
    }

    public function viewRoom($value, $kosId){
        $this->roomIndex = [];
        $this->selectedKos = $kosId;
        $selectedKos = $this->emptyKos['id'][$kosId];

        $this->getRoom = $value;
        // dd($kosId);
        // $kosData[array_search($selectedKos, array_column($kosData, 'id'))]['name']
        foreach($this->emptyRoom as $empty => $room){
            // dd($room['kosId']);
            if($room['kosId'] == $selectedKos){
                $this->roomIndex[] = $empty;
            }
            // $this->roomIndex[] = array_search($kosId, array_column($this->emptyRoom, 'kosId'));
        }
        // dd($this->roomIndex);
    }

    public function requestRoom($roomId){
        $this->selectedRoom = $roomId;
    }

    public function confirmSelect(){
        $oldRoom = Room::where('userId', Auth::user()->id)->value('id');
        if($oldRoom){
            Room::where('userId', Auth::user()->id)->update(['userId' => null]);
            RoomBill::where('userId', Auth::user()->id)->delete();
        }

        $oldData = UserRequest::where('userId', Auth::user()->id)->value('roomId');
        if($oldData){
            $deleteOld = UserRequest::where('userId', Auth::user()->id)->delete();
            if(!$deleteOld){
                session()->flash('error', 'Gagal membuat permintaan, coba lagi');
                return;
            }
        }

        $requesRoom = UserRequest::create([
            'roomId' => $this->selectedRoom,
            'userId' => Auth::user()->id,
        ]);
        if($requesRoom){
            $this->getRoom = false;
            $this->render();
            session()->flash('message', 'Berhasil membuat permintaan');
        } else{
            session()->flash('error', 'Gagal membuat permintaan, coba lagi');
        }
    }

    public function deleteRequest(){
        $oldRoom = Room::where('userId', Auth::user()->id)->value('id');
        if($oldRoom){
            Room::where('userId', Auth::user()->id)->update(['userId' => null]);
            RoomBill::where('userId', Auth::user()->id)->delete();
        }
        UserRequest::where('userId', Auth::user()->id)->delete();

        $this->getRoom = false;
        $this->render();
        session()->flash('message', 'Berhasil menghapus data');
    }
}
