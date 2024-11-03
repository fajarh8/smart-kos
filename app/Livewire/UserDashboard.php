<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserDashboard extends Component
{
    public $userId;
    public $roomId;
    public $kosName;
    public $roomName;
    public function render()
    {
        $this->userId = Auth::user()->id;
        $this->roomId = User::find($this->userId)->room->id;
        $this->roomName = User::find($this->userId)->room->name;
        $this->kosName = Room::find($this->roomId)->kos->name;

        return view('livewire.user-dashboard');
    }
}
