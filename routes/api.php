<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\RelayController;
use App\Http\Controllers\SensorDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Route::post('/registerdevice', [DeviceController::class, 'registerDevice']);
Route::patch('/updatesensordata', [SensorDataController::class, 'update']);
Route::patch('/updaterelaystatus', [RelayController::class, 'update']);
Route::post('/getsensordata', [SensorDataController::class, 'show']);
Route::post('/getrelaydata', [RelayController::class, 'show']);
// Route::post('/gettimezone', [DeviceController::class, 'getTimezone']);
