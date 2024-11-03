<?php

namespace App\Http\Controllers;

use App\Events\RelayStatusUpdated;
use App\Events\SensorDataUpdated;
use App\Models\IotDevice;
use App\Models\KwhHistory;
use App\Models\Relay;
use App\Models\Room;
use App\Models\RoomBill;
use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RelayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Missing Request',
                'data' => $validator->errors()
            ], 400);
        }

        $deviceId = IotDevice::where('token', $request->input('token'))->value('id');
        $roomId = IotDevice::where('id', $deviceId)->value('roomId');
        $userId = Room::where('id', $roomId)->value('userId');
        if(!$deviceId){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Name and Token'
            ], 401);
        } else{
            // if($checkToken){
                for($i=1; $i<=6; $i++){
                    $relayStatus[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('status');
                    $relayCategory[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('categoryId');
                    if($relayCategory[$i] == 2){
                        $threshold[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('tempThreshold');
                    } else{
                        $threshold[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('ldrThreshold');
                    }
                    $relayAutomation[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('automation');
                    $relayPir[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('pirAuto');
                    $relayOnHour[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('on_hour');
                    $relayOffHour[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('off_hour');
                    $relayOnMinute[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('on_minute');
                    $relayOffMinute[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('off_minute');
                    $relayturnedOn[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('turnedOn');
                    $relayturnedOff[$i] = Relay::where([['deviceId', $deviceId], ['number', $i]])->value('turnedOff');
                }
                $timezone = Room::where('id', $roomId)->value('timezone');
                $maxPower = Room::where('id', $roomId)->value('power');
                $pirStatus = SensorData::where([['deviceId', $deviceId], ['category', 'pir']])->value('data');
                $lastHour = KwhHistory::where([['userId', $userId], ['roomId', $roomId]])->orderBy('id', 'desc')->value('hour');
                $lastDay = KwhHistory::where([['userId', $userId], ['roomId', $roomId]])->orderBy('id', 'desc')->value('day');
                $lastMonth = KwhHistory::where([['userId', $userId], ['roomId', $roomId]])->orderBy('id', 'desc')->value('month');
                $lastYear = KwhHistory::where([['userId', $userId], ['roomId', $roomId]])->orderBy('id', 'desc')->value('year');
                // $ldrThreshold = RoomBill::where('roomId', $roomId)->value('ldrThreshold');
                // $tempThreshold = RoomBill::where('roomId', $roomId)->value('tempThreshold');
                $pirOnHour = RoomBill::where('roomId', $roomId)->value('pirOnHour');
                $pirOnMin = RoomBill::where('roomId', $roomId)->value('pirOnMin');
                $pirOffHour = RoomBill::where('roomId', $roomId)->value('pirOffHour');
                $pirOffMin = RoomBill::where('roomId', $roomId)->value('pirOffMin');
                $pirInterval = RoomBill::where('roomId', $roomId)->value('pirInterval');
                $pirSchedule = RoomBill::where('roomId', $roomId)->value('pirSchedule');

                return response()->json([
                    // 'ldrThreshold'  => $ldrThreshold,
                    'timezone'      => $timezone,
                    'lastHour'      => $lastHour,
                    'pirStatus'     => $pirStatus,
                    'lastDay'       => $lastDay,
                    'lastMonth'     => $lastMonth,
                    'lastYear'      => $lastYear,
                    'maxPower'      => $maxPower,
                    'pirOnHour'     => $pirOnHour,
                    'pirOnMin'      => $pirOnMin,
                    'pirOffHour'    => $pirOffHour,
                    'pirOffMin'     => $pirOffMin,
                    'pirInterval'   => $pirInterval,
                    'pirSchedule'   => $pirSchedule,
                    'status'        => [$relayStatus[1],        $relayStatus[2],        $relayStatus[3], $relayStatus[4], $relayStatus[5], $relayStatus[6]],
                    'category'      => [$relayCategory[1],      $relayCategory[2],      $relayCategory[3], $relayCategory[4], $relayCategory[5], $relayCategory[6]],
                    'threshold'     => [$threshold[1],          $threshold[2],          $threshold[3], $threshold[4], $threshold[5], $threshold[6]],
                    'automation'    => [$relayAutomation[1],    $relayAutomation[2],    $relayAutomation[3], $relayAutomation[4], $relayAutomation[5], $relayAutomation[6]],
                    'onHour'        => [$relayOnHour[1],        $relayOnHour[2],        $relayOnHour[3], $relayOnHour[4], $relayOnHour[5], $relayOnHour[6]],
                    'offHour'       => [$relayOffHour[1],       $relayOffHour[2],       $relayOffHour[3], $relayOffHour[4], $relayOffHour[5], $relayOffHour[6]],
                    'onMinute'      => [$relayOnMinute[1],      $relayOnMinute[2],      $relayOnMinute[3], $relayOnMinute[4], $relayOnMinute[5], $relayOnMinute[6]],
                    'offMinute'     => [$relayOffMinute[1],     $relayOffMinute[2],     $relayOffMinute[3], $relayOffMinute[4], $relayOffMinute[5], $relayOffMinute[6]],
                    'pirAuto'       => [$relayPir[1],           $relayPir[2],           $relayPir[3], $relayPir[4], $relayPir[5], $relayPir[6]],
                    'turnedOn'      => [$relayturnedOn[1],      $relayturnedOn[2],      $relayturnedOn[3], $relayturnedOn[4], $relayturnedOn[5], $relayturnedOn[6]],
                    'turnedOff'     => [$relayturnedOff[1],     $relayturnedOff[2],     $relayturnedOff[3], $relayturnedOff[4], $relayturnedOff[5], $relayturnedOff[6]]
                ], 200);
            // } else {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Invalid Name and Token'
            //     ], 401);
            // }
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Relay $relay)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'token' => 'required',
            'relayStatus' => 'required|boolean',
            'relayAuto' => 'required|boolean',
            'relayNumber' => 'required',
            'turnedOn' => 'required|boolean',
            'turnedOff' => 'required|boolean',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Missing Request',
                'data' => $validator->errors()
            ], 400);
        } else{
            $requestData = [
                'deviceName' => $request->input('name'),
                'deviceToken' => $request->input('token'),
                'tatus' => $request->input('relayStatus'),
                'auto' => $request->input('relayAuto'),
                'number' => $request->input('relayNumber'),
                'turnedOn' => $request->input('turnedOn'),
                'turnedOff' => $request->input('turnedOff'),
            ];
        }

        $deviceId = IotDevice::where('name', $requestData['deviceName'])->value('id');
        if(!$deviceId){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Name and Token'
            ], 401);
        } else{
            $userId = IotDevice::find($deviceId)->room()->value('userId');
            $checkToken = Hash::check($requestData['deviceToken'], (IotDevice::where('name', $requestData['deviceName'])->value('token')));
            if($checkToken){
                $updateData = IotDevice::find($deviceId)->relay()->where('number', $requestData['number'])->update([
                    'status' => $request->input('relayStatus'),
                    'automation' => $request->input('relayAuto'),
                    'turnedOn' => $request->input('turnedOn'),
                    'turnedOff' => $request->input('turnedOff')
                ]);
                if($updateData){
                    // SensorDataUpdated::dispatch($userId, 'relay');

                    return response()->json([
                        'status' => true,
                        'message' => 'Update Data Success',
                        'data' => [$deviceId, $updateData]
                    ], 200);
                } else{
                    return response()->json([
                        'status' => false,
                        'message' => 'Data Not Found'
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Name and Token'
                ], 401);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
    }
}
