<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    function index(){
        return view('dashboard');
    }
    function admin(){
        return view('admin');
    }
    function user(){
        return view('user');
    }

    function kosManagement(){
        return view('admin.kosManagement');
    }
}
