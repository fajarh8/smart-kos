<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Login extends Component
{
    public $email = '';
    public $name = '';
    public $password = '';
    public $password_confirmation = '';
    public $loginData = [];
    public $create = false;
    public $role = 'user';
    protected $listeners = ['enter' => 'enterEvent'];

    public function render()
    {
        return view('livewire.login');
    }

    public function enterEvent(){
        if($this->create == false){
            $this->loginUser();
        } else{
            $this->submitCreate();
        }
    }

    public function loginUser(){
        $this->loginData = $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password wajib diisi'
        ]);

        if(Auth::attempt($this->loginData)){
            if(Auth::user()->role == 'admin'){
                return redirect('/dashboard/admin/kos');
            } else if(Auth::user()->role == 'user'){
                return redirect('/dashboard/user');
            }
        } else{
            // $this->render();
            session()->flash('loginError', 'Email/Password salah');
        }
    }

    public function submitCreate(){
        $loginData = $this->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
            'password' => 'required|confirmed',
            'role' => 'required'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email telah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.confirmed' => 'Password tidak cocok',
            'role.required' => 'Silakan pilih role',
        ]);
// dd($loginData);
        // if($this->password != $this->passwordConfirm){
            $createAttemp = User::create($loginData);
            if($createAttemp){
                $this->createAccount(false);
                session()->flash('createSuccess', 'Berhasil membuat akun, silakan Login');

                // if(Auth::attempt($loginData)){
                //     if(Auth::user()->role == 'admin'){
                //         return redirect('/dashboard/admin/kos');
                //     } elseif(Auth::user()->role == 'user'){
                //         return redirect('/dashboard/user');
                //     }
                // } else{
                //     return redirect('')->withErrors('Invalid Email/Password')->withInput();
                // }
            } else{
                session()->flash('loginError', 'Silakan coba buat akun lagi');
            }
        // } else{
        //     session()->flash('error', 'Password tidak cocok');
        // }
    }

    public function createAccount($value){
        $this->email = '';
        $this->name = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'user';
        $this->create = $value;
    }

    public function selectRole($value){
        // dd($value);
        $this->role = $value;
    }
}
