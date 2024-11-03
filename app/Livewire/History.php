<?php

namespace App\Livewire;

use App\Models\Kos;
use App\Models\KwhHistory;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
//use App\Events\PowerIsOver;

class History extends Component
{
    public $chartData,
        $userId,
        $homeView = true,
        $roomId,
        $selectedDate,
        $selectedDailyTariff,
        $selectedTariff,
        $selectedStatus,
        // $date,
        // $month,
        $manyYear,
        $roomName,
        $kosName,
        $selectedYear,
        $selectedMonth,
        $selectedDay,
        $totalYear,
        $selectedKwh,
        $yearlyKwh,
        $selectedBill,
        $yearlyBill,
        $roomTariff,
        $manyRoom,
        // $year,
        // $monthEnd,
        $mode;
    public function render()
    {
	//PowerIsOver::dispatch(1);
        $this->userId = Auth::user()->id;
        // $this->roomId = Room::where('userId', $this->userId)->value('id');
        date_default_timezone_set('Asia/Jakarta');

        $roomData = [];
        foreach(KwhHistory::where('userId', $this->userId)->get('roomId') as $data => $value){
            if(!in_array($value->roomId, $roomData)){
                $roomData[] = $value->roomId;
            }
        }

        if($roomData){
            foreach($roomData as $room => $id){
                $this->manyRoom[$room]['id'] = $id;
                $this->manyRoom[$room]['name'] = Room::where('id', $id)->value('name');
                $kos = Room::where('id', $id)->value('kosId');
                $this->manyRoom[$room]['kos'] = Kos::where('id', $kos)->value('name');
                $adminId = Kos::where('id', $kos)->value('adminId');
                $this->manyRoom[$room]['admin'] = User::where('id', $adminId)->value('name');
            }
        }

        // $this->selectMode(0, $this->year.'-'.$this->month);
        return view('livewire.history');
    }

    public function roomDetail($roomId, $value){
        $this->mode = 0;
        $this->homeView = $value;
        if($value == false){
            $this->roomId = $roomId;
            $this->roomName = Room::where('id', $roomId)->value('name');
            $this->kosName = Kos::where('id', Room::where('id', $roomId)->value('kosId'))->value('name');
            foreach(KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId]])->get() as $datas){
                $this->manyYear[] = $datas->year;
            }
            if($this->manyYear){
                $this->manyYear = array_unique($this->manyYear);
                sort($this->manyYear);

                $this->totalYear = count($this->manyYear);
                for($i=0; $i<$this->totalYear; $i++){
                    $this->yearlyBill[$i] = 0;
                    $this->yearlyKwh[$i] = 0;
                    $firstMonth = KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['year', $this->manyYear[$i]]])->orderBy('id', 'asc')->value('month');
                    $this->roomTariff[$i] = KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['year', $this->manyYear[$i]]])->orderBy('id', 'desc')->value('tariff');
                    // dd($this->roomTariff);

                    for($j=$firstMonth; $j<=12; $j++){
                        $a_date = $this->manyYear[$i].'-'.$j;
                        $lastMonth = (int)date("t", strtotime($a_date));
                        // dd($a_date);
                        $this->yearlyBill[$i] += KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['year', $this->manyYear[$i]], ['month', $j], ['day', $lastMonth+1]])->value('bill');
                        $this->yearlyKwh[$i] += KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['year', $this->manyYear[$i]], ['month', $j], ['day', $lastMonth+1]])->value('kwh');
                    }
                }
            }
        }
    }

    public function selectMode($mode, $dateInput){
        $this->mode = $mode;

        if($this->selectedDate !== null){
            array_splice($this->selectedDate, 0, count($this->selectedDate));
        }
        if($this->selectedTariff !== null){
            array_splice($this->selectedTariff, 0, count($this->selectedTariff));
        }
        if($this->selectedKwh !== null){
            array_splice($this->selectedKwh, 0, count($this->selectedKwh));
        }
        if($this->selectedBill !== null){
            array_splice($this->selectedBill, 0, count($this->selectedBill));
        }
        $this->selectedStatus = [];


        switch($mode){
            case 1:
                $this->selectedYear = $dateInput;
                foreach(KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['year', $dateInput]])->get() as $data){
                    $manyMonth[] = $data->month;
                }
                $manyMonth = array_unique($manyMonth);
                sort($manyMonth);
                // dd($manyMonth);

                $thisYear = idate('Y');
                $thisMonth = idate('m');
                foreach($manyMonth as $month => $value){
                    // dd($value);
                    $this->selectedDate[] = date_format(date_create($dateInput .'-'. $value), 'm/Y');
                    $this->selectedTariff[] = KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['year', $dateInput], ['month', $value]])->orderBy('id', 'desc')->value('tariff');
                    // $lastDay = (int)date("t", strtotime($dateInput .'-'. $value));
                    // dd($lastDay);
                    $this->selectedKwh[] = KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['year', $dateInput], ['month', $value]])->orderBy('day', 'desc')->value('kwh');
                    $this->selectedBill[] = KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['year', $dateInput], ['month', $value]])->orderBy('day', 'desc')->value('bill');
                    if($value == $thisMonth && $dateInput == $thisYear){
                        $this->selectedStatus[] = null;
                    } else{
                        $this->selectedStatus[] = KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['year', $dateInput], ['month', $value]])->orderBy('day', 'desc')->value('isPaid');
                    }
                }
                // dd($this->selectedBill);

                // dd($this->selectedDate);
                break;
            case 2:
                $this->selectedMonth = $dateInput;
                $lastDay = (int)date("t", strtotime($this->selectedYear .'-'. $dateInput));
                // dd($lastDay);
                foreach(KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['year', $this->selectedYear], ['month', $dateInput]])->get() as $data){
                    $manyDay[] = $data->day;
                }
                $manyDay = array_unique($manyDay);
                sort($manyDay);
                // dd($manyDay);

                $index = array_search($lastDay+1, $manyDay);
                if($index){
                    unset($manyDay[$index]);
                }
                // dd($manyDay);
                // dd($this->selectedDate);
                for($i=0; $i<count($manyDay); $i++){
                    $this->selectedDate[$i] = date_format(date_create($this->selectedYear .'-'. $dateInput .'-'. $manyDay[$i]), 'd/m/Y');
                    $this->selectedTariff[$i] = KwhHistory::where([['roomId', $this->roomId], ['userId', $this->userId], ['year', $this->selectedYear], ['month', $dateInput], ['day', $manyDay[$i]]])->orderBy('id', 'desc')->value('tariff');
                    $this->selectedKwh[$i] = KwhHistory::where([['userId', $this->userId], ['roomId', $this->roomId], ['day', $manyDay[$i]], ['month', $dateInput], ['year', $this->selectedYear]])->orderBy('id', 'desc')->value('kwh');
                    $this->selectedBill[$i] = KwhHistory::where([['userId', $this->userId], ['roomId', $this->roomId], ['day', $manyDay[$i]], ['month', $dateInput], ['year', $this->selectedYear]])->orderBy('id', 'desc')->value('bill');
                }
                // dd($this->selectedDate);
                break;
            case 3:
                foreach(KwhHistory::where([['userId', $this->userId], ['roomId', $this->roomId], ['day', $dateInput], ['month', $this->selectedMonth], ['year', $this->selectedYear]])->get() as $data){
                    $manyHour[] = $data->hour;
                }
                $manyHour = array_unique($manyHour);
                sort($manyHour);
                $bigest = count($manyHour);
                $bigest = $manyHour[$bigest-1];
                // dd($start);
                $start = $manyHour[0];
                for($i=0; $i<$bigest; $i++){
                    $manyHour[$i] = null;
                }
                // dd($manyHour);
                // dd(count($manyHour));

                foreach($manyHour as $hour => $value){
                    if($hour >= ($start - 1)){
                        if($hour+1 == 24){
                            $dateCreate = date_create($this->selectedYear .'-'. $this->selectedMonth .'-'. $dateInput);
                            $dateFormat = date_format($dateCreate, 'd/m/Y');
                            $this->selectedDate[] = $dateFormat.' 24:00';
                        } else{
                            $dateCreate = date_create($this->selectedYear .'-'. $this->selectedMonth .'-'. $dateInput .' '. $hour+1 .':0');
                            if($dateCreate){
                                $this->selectedDate[] = date_format($dateCreate, 'd/m/Y H:i');
                            } else{
                                $this->selectedDate[] = "date_format($dateCreate, 'd/m/Y H:i')";
                            }
                        }
                        // dd($this->selectedDate[$i]);
                        $tariff = KwhHistory::where([['userId', $this->userId], ['roomId', $this->roomId], ['hour', $hour+1], ['day', $dateInput], ['month', $this->selectedMonth], ['year', $this->selectedYear]])->value('tariff');
                        if($tariff){
                            $this->selectedTariff[] = $tariff;
                        } else{
                            if($hour == 0){
                                $this->selectedTariff[] = 0;
                            } else{
                                $this->selectedTariff[] = $this->selectedTariff[$hour-1];
                            }
                        }
                        $kwh = KwhHistory::where([['userId', $this->userId], ['roomId', $this->roomId], ['hour', $hour+1], ['day', $dateInput], ['month', $this->selectedMonth], ['year', $this->selectedYear]])->value('kwh');
                        if($kwh){
                            $this->selectedKwh[] = $kwh;
                        } else{
                            if($hour == 0){
                                $this->selectedKwh[] = 0;
                            } else{
                                $this->selectedKwh[] = $this->selectedKwh[$hour-1];
                            }
                        }
                        $bill = KwhHistory::where([['userId', $this->userId], ['roomId', $this->roomId], ['hour', $hour+1], ['day', $dateInput], ['month', $this->selectedMonth], ['year', $this->selectedYear]])->value('bill');
                        if($bill){
                            $this->selectedBill[] = $bill;
                        } else{
                            if($hour == 0){
                                $this->selectedBill[] = 0;
                            } else{
                                $this->selectedBill[] = $this->selectedBill[$hour-1];
                            }
                        }
                    } else{
                        unset($manyHour[$hour]);
                    }
                }
                // for($i=0; $i<count($manyHour); $i++){
                //     // if()
                //     if($i+1 == 24){
                //         $dateCreate = date_create($this->selectedYear .'-'. $this->selectedMonth .'-'. $dateInput);
                //         $this->selectedDate[$i] = date_format($dateCreate, 'd/m/Y');
                //         $this->selectedDate[$i] = $this->selectedDate[$i].' 24:00';
                //     } else{
                //         $dateCreate = date_create($this->selectedYear .'-'. $this->selectedMonth .'-'. $dateInput .' '. $i+1 .':0');
                //         if($dateCreate){
                //             $this->selectedDate[$i] = date_format($dateCreate, 'd/m/Y H:i');
                //         } else{
                //             $this->selectedDate[$i] = "date_format($dateCreate, 'd/m/Y H:i')";
                //         }
                //     }
                //     // dd($this->selectedDate[$i]);
                //     $this->selectedTariff[$i] = KwhHistory::where([['userId', $this->userId], ['roomId', $this->roomId], ['hour', $i+1], ['day', $dateInput], ['month', $this->selectedMonth], ['year', $this->selectedYear]])->value('tariff');
                //     $this->selectedKwh[$i] = KwhHistory::where([['userId', $this->userId], ['roomId', $this->roomId], ['hour', $i+1], ['day', $dateInput], ['month', $this->selectedMonth], ['year', $this->selectedYear]])->value('kwh');
                //     $this->selectedBill[$i] = KwhHistory::where([['userId', $this->userId], ['roomId', $this->roomId], ['hour', $i+1], ['day', $dateInput], ['month', $this->selectedMonth], ['year', $this->selectedYear]])->value('bill');
                // }
                break;
            case 0:
                $this->render();
                break;
            default:
                break;
        }
    }
}
