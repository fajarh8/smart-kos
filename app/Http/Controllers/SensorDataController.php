<?php

namespace App\Http\Controllers;

use App\Events\SensorDataUpdated;
use App\Jobs\UpdateSensorDataJob;
use App\Models\IotDevice;
use App\Models\Room;
use App\Models\SensorData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SensorDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public $userId;
    public $roomId;
    public function index()
    {
        $this->userId = Auth::user()->id;
        // dd($this->userId);
        $this->roomId = Room::where('userId', $this->userId)->value('id');
        if($this->roomId === null){
            return redirect('/dashboard/user/kossearch');
        } else{
            $this->roomId = User::find($this->userId)->room->id;
            $roomName = User::find($this->userId)->room->name;
            $kosName = Room::find($this->roomId)->kos->name;
        }
        return view('user.user', compact(
            'kosName',
            'roomName'
        ));
    }

    public function automation()
    {
        $this->userId = Auth::user()->id;
        $this->roomId = Room::where('userId', $this->userId)->value('id');
        if($this->roomId === null){
            return redirect('/dashboard/user/kossearch');
        } else{
            $this->roomId = User::find($this->userId)->room->id;
            $roomName = User::find($this->userId)->room->name;
            $kosName = Room::find($this->roomId)->kos->name;
        }
        return view('user.editAutomation', compact(
            'kosName',
            'roomName'
        ));
    }

    public function history()
    {
        $this->userId = Auth::user()->id;
        $this->roomId = Room::where('userId', $this->userId)->value('id');
        if($this->roomId === null){
            return redirect('/dashboard/user/kossearch');
        } else{
            $this->roomId = User::find($this->userId)->room->id;
            $roomName = User::find($this->userId)->room->name;
            $kosName = Room::find($this->roomId)->kos->name;
        }
        return view('user.history', compact(
            'kosName',
            'roomName'
        ));
    }

    public function userProfile()
    {
        $this->userId = Auth::user()->id;$roomData = Room::where('userId', $this->userId)->value('id');
        $roomData = Room::where('userId', $this->userId)->value('id');
        if($roomData){
            $this->roomId = User::find($this->userId)->room->id;
            $roomName = User::find($this->userId)->room->name;
            $kosName = Room::find($this->roomId)->kos->name;
        } else{
            $this->roomId = null;
            $roomName = null;
            $kosName = null;
        }
        return view('user.profile', compact(
            'kosName',
            'roomName'
        ));
    }
    public function kosSearch()
    {
        $this->userId = Auth::user()->id;
        $roomData = Room::where('userId', $this->userId)->value('id');
        if($roomData){
            $this->roomId = User::find($this->userId)->room->id;
            $roomName = User::find($this->userId)->room->name;
            $kosName = Room::find($this->roomId)->kos->name;
        } else{
            $this->roomId = null;
            $roomName = null;
            $kosName = null;
        }
        return view('user.kosSearch', compact(
            'kosName',
            'roomName'
        ));
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
        $requestData = [
            'deviceName' => 'required',
            'deviceToken' => 'required',
            'dataCategory' => 'required|exists:sensor,category',
        ];

        $validator = Validator::make($request->all(), $requestData);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Missing Request',
                'data' => $validator->errors()
            ], 400);
        }

        $deviceName = $request->input('deviceName');
        $deviceToken = $request->input('deviceToken');
        $dataCategory = $request->input('dataCategory');
        $deviceId = IotDevice::where('name', $deviceName)->value('id');

        if(!$deviceId){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Name and Token'
            ], 401);
        } else{
            $checkToken = Hash::check($deviceToken, (IotDevice::where('name', $deviceName)->value('token')));
            if($checkToken){
                $dataValue = IotDevice::find($deviceId)->sensor()->where('category', $dataCategory)->value('data');
                return response()->json([
                    'status' => true,
                    'message' => $dataValue
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Name and Token',
                ], 401);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SensorData $sensorData)
    {
        //
    }

    /**
     * @param Request $request
     * @return array
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'token' => 'required',
            'category' => 'required|exists:sensor,category',
            'value' => 'required'
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
                'dataCategory' => $request->input('category'),
                'dataValue' => $request->input('value'),
            ];
        }

        $deviceId = IotDevice::where('name', $requestData['deviceName'])->value('id');
        if(!$deviceId){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Name and Token'
            ], 401);
        } else{
            $checkToken = Hash::check($requestData['deviceToken'], (IotDevice::where('name', $requestData['deviceName'])->value('token')));
            if($checkToken){
                $updateData = IotDevice::find($deviceId)->sensor()->where('category', $requestData['dataCategory'])->update([
                    'data' => $request->input('value')
                ]);
                if($updateData){
                    $userId = IotDevice::find($deviceId)->room()->value('userId');
                    // SensorDataUpdated::dispatch($userId, $requestData['dataCategory']);

                    return response()->json([
                        'status' => true,
                        'message' => 'Update Data Success',
                        'data' => [$userId, $requestData['dataCategory']]
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
    public function destroy(SensorData $sensorData)
    {
        //
    }
}
