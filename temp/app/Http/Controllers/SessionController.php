<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    function index(){
        return view('login');
    }

    // function login(Request $request){
    //     $request->validate([
    //         'email' => 'required',
    //         'password' => 'required'
    //     ], [
    //         'email.required' => 'Email wajib diisi',
    //         'password.required' => 'Password wajib diisi'
    //     ]);

    //     $loginValidation = [
    //         'email' => $request -> email,
    //         'password' => $request -> password
    //     ];

    //     if(Auth::attempt($loginValidation)){
    //         if(Auth::user()->role == 'admin'){
    //             return redirect('/dashboard/admin/kos');
    //         } elseif(Auth::user()->role == 'user'){
    //             return redirect('/dashboard/user');
    //         }
    //     } else{
    //         return redirect('')->withErrors('Invalid Email/Password')->withInput();
    //     }
    // }

    function logout(Request $request){
        Auth::logout();
        return redirect('/');
    }

    // function profile(){
    //     return view('profile');
    // }
}
