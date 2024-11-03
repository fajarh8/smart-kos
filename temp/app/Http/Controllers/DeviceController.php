<?php

namespace App\Http\Controllers;

use App\Models\IotDevice;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    public function registerDevice(Request $request){
        $registerDevice = new IotDevice();

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:iot_device,name',
            'token' => 'required',
            'roomId' => 'required|exists:room,id'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Register Failed',
                'data' => $validator->errors()
            ], 400);
        }

        $registerDevice->name = $request->name;
        $registerDevice->token = bcrypt($request->token);
        $registerDevice->roomId = $request->roomId;
        $registerDevice->save();

        return response()->json([
            'status' => true,
            'message' => 'Registration Success'
        ], 200);
    }
    public function loginDevice(Request $request){
        //
    }
    public function getTimezone(Request $request){
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Missing Request',
                'data' => $validator->errors()
            ], 400);
        } else{
            $requestData = [
                'token' => $request->input('token'),
            ];
        }

        $deviceId = IotDevice::where('token', $requestData['token'])->value('id');
        if(!$deviceId){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Name and Token'
            ], 401);
        } else{
            // $checkToken = Hash::check($requestData['deviceToken'], (IotDevice::where('name', $requestData['deviceName'])->value('token')));
            $roomId = IotDevice::where('id', $deviceId)->value('roomId');
            $timezone = Room::where('id', $roomId)->value('timezone');
            if($timezone){
                return response()->json([
                    'status' => true,
                    'data' => $timezone
                ], 200);
            } else{
                return response()->json([
                    'status' => false,
                    'message' => 'Data Not Found'
                ], 404);
            }
        }
    }
}
