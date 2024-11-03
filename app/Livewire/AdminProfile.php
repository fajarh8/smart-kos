<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdminProfile extends Component
{
    public $isEditing = false;
    public $startName;
    public $startEmail;
    public $name;
    public $userId;
    public $email;
    public $password = null;
    public $password_confirmation = null;
    public function render()
    {
        $this->userId = Auth::user()->id;
        foreach(User::where('id', $this->userId)->get(['name', 'email', 'password']) as $user => $data){
            $this->startName = $data->name;
            $this->startEmail = $data->email;
            $password = $data->password;
        }
        return view('livewire.admin-profile');
    }

    public function edit($status){
        $this->isEditing = $status;
        if($status == true){
            $this->name = $this->startName;
            $this->email = $this->startEmail;
        } else{
            $this->name = null;
            $this->email = null;
            $this->password = null;
            $this->password_confirmation = null;
            $this->render();
        }
        // dd($this->isEditing);
    }

    public function saveData(){
        // dd($this->password);
        if($this->password == null && $this->password_confirmation == null){
            $validated = $this->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$this->userId,
            ], [
                'name.required' => 'Nama tidak boleh kosong',
                'email.required' => 'Email tidak boleh kosong',
                'email.email' => 'Email tidak valid',
                'email.unique' => 'Email sudah digunakan',
            ]);
        } else{
            $validated = $this->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$this->userId,
                'password' => 'required|confirmed',
            ], [
                'name.required' => 'Nama tidak boleh kosong',
                'email.required' => 'Email tidak boleh kosong',
                'email.email' => 'Email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'password.required' => 'Password wajib diisi',
                'password.confirmed' => 'Password tidak cocok',
            ]);
            $validated['password'] = bcrypt($validated['password']);
        }
        User::where('id', $this->userId)->update($validated);
        $this->edit(false);
        session()->flash('message', 'Berhasil update data');
    }
}
