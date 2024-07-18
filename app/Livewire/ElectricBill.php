<?php

namespace App\Livewire;

use App\Models\Kos;
use App\Models\KwhHistory;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ElectricBill extends Component
{
    public $mode = 0;
    public $currentMonth = 0;
    public $currentYear = 0;
    public $kosData;
    public $roomData;
    public $roomUser = [];
    public $selectedUser;
    public $selectedYear;
    public $selectedMonth;
    public $selectedDay;
    public $roomId;
    public $selectedBill;
    public $selectedKos;
    public $selectedRoom;
    public $testData;
    public $yearlyData = [];
    public $monthlyData = [];
    public $dailyData = [];
    public $hourlyData = [];
    public $getDetail = false;
    public function render()
    {
        $this->kosData = [];
        $this->roomData = [];
        foreach(Kos::where('adminId', Auth::user()->id)->get(['id', 'name', 'address']) as $kos => $data){
            $this->kosData[] = [
                'id' => $data->id,
                'name' => $data->name,
                'address' => $data->address,
            ];
        }

        foreach($this->kosData as $kos => $value){
            foreach(Room::where('kosId', $value['id'])->get(['id', 'name', 'userId', 'timezone']) as $room => $data){
                $this->roomData[$kos][] = [
                    'id' => $data->id,
                    'name' => $data->name,
                    'userId' => $data->userId,
                    'userName' => User::where('id', $data->userId)->value('name'),
                    'timezone' => $data->timezone,
                ];
            }
        }

        switch($this->roomData[0][0]['timezone']){
            case 7:
                $timezone = 'Asia/Jakarta';
                break;
            case 8:
                $timezone = 'Asia/Makassar';
                break;
            case 9:
                $timezone = 'Asia/Jayapura';
                break;
        }
        date_default_timezone_set($timezone);

        return view('livewire.electric-bill');
    }

    public function getRoom($roomId, $value, $kosId){
        $this->getDetail = $value;
        $this->yearlyData = [];
        $this->monthlyData = [];
        $this->dailyData = [];
        $this->hourlyData = [];
        $this->roomUser = [];
        $this->selectedKos = $kosId;
        $this->selectedRoom = $roomId;
        if($value == true){
            $roomUser = [];
            foreach(KwhHistory::where([['roomId', $this->selectedRoom]])->get(['userId', 'year']) as $data => $user){
                if(!in_array($user->userId, $roomUser)){
                    $roomUser[] = $user->userId;
                }
            }

            if($roomUser){
                foreach($roomUser as $room => $user){
                    $this->roomUser[] = [
                        'userId' => $user,
                        'userName' => User::where('id', $user)->value('name'),
                        'yearStart' => KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $user]])->orderBy('year', 'asc')->value('year'),
                        'yearEnd' => KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $user]])->orderBy('year', 'desc')->value('year'),
                    ];
                }
            }
        }else{
            $this->selectedKos = 0;
            $this->selectedRoom = 0;
            $this->roomUser = [];
            $this->render();
        }
    }

    public function billDetail($value, $mode){
        $this->mode = $mode;
        switch($mode){
            case 0:
                $this->selectedUser = 0;
                $this->selectedDay = 0;
                $this->selectedMonth = 0;
                $this->selectedYear = 0;
                $this->getRoom($this->selectedRoom, true, $this->selectedKos);
                break;
            case 1:
                $this->yearlyData = [];
                $this->monthlyData = [];
                $this->dailyData = [];
                $this->hourlyData = [];
                $this->selectedDay = 0;
                $this->selectedMonth = 0;
                $this->selectedYear = 0;
                if($value != 0){
                    $this->selectedUser = $value;
                }
                $this->currentYear = idate('Y');
                $this->currentMonth = idate('m');
                // dd(date_format(date_create($this->currentYear.'-'. $this->month), 'm/Y'));
                $manyYear = [];
                foreach(KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser]])->orderBy('year', 'asc')->get('year') as $year => $data){
                    if(!in_array($data->year, $manyYear)){
                        $manyYear[] = $data->year;
                    }
                }
                // dd($manyYear);

                if($manyYear){
                    foreach($manyYear as $year => $data){
                        $this->yearlyData[$year] = [
                            'date' => $data,
                            'tariff' => KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $data]])->value('tariff'),
                        ];
                    }
                    // dd($manyYear);
                    foreach($this->yearlyData as $yearly => $data){
                        $this->yearlyData[$yearly]['bill'] = 0;
                        $this->yearlyData[$yearly]['kwh'] = 0;
                        $firstMonth = KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $data['date']]])->orderBy('month', 'asc')->value('month');
                        // dd($firstMonth);
                        for($i=$firstMonth; $i<=12; $i++){
                            // $a_date = $this->yearlyData[$yearly]['date'].'-'.$i;
                            // $lastMonth = (int)date("t", strtotime($a_date));
                            $this->yearlyData[$yearly]['kwh'] += KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->yearlyData[$yearly]['date']], ['month', $i]])->orderBy('day', 'desc')->value('kwh');
                            $isPaid = KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->yearlyData[$yearly]['date']], ['month', $i]])->orderBy('day', 'desc')->value('isPaid');
                            if($this->yearlyData[$yearly]['date'] === $this->currentYear && $i === $this->currentMonth){
                                //
                            } else{
                                if($isPaid === 0){
                                    $this->yearlyData[$yearly]['bill']++;
                                }
                            }
                        }
                    }
                }
                // dd($this->yearlyData);

                break;
            case 2:
                $this->monthlyData = [];
                $this->dailyData = [];
                $this->hourlyData = [];
                $this->selectedDay = 0;
                $this->selectedMonth = 0;
                if($value != 0){
                    $this->selectedYear = $value;
                    // $this->selectedDate = $value;
                }

                $manyMonth = [];
                foreach(KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear]])->get('month') as $month => $value){
                    if(!in_array($value->month, $manyMonth)){
                        $manyMonth[] = $value->month;
                    }
                }
                // dd($manyMonth);
                if($manyMonth){
                    foreach($manyMonth as $month => $data){
                        $this->monthlyData[$month] = [
                            'date' => date_format(date_create($this->selectedYear .'-'. $data), 'm/Y'),
                            'tariff' => KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', $data]])->value('tariff'),
                        ];
                    }
                    // dd($this->monthlyData[$month]);
                    foreach($this->monthlyData as $monthly => $data){
                        // $a_date = $this->selectedYear.'-'.(int)$data['date'];
                        // $lastMonth = (int)date("t", strtotime($a_date));

                        $this->monthlyData[$monthly]['kwh'] = KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', (int)$data['date']]])->orderBy('day', 'desc')->value('kwh');
                        $this->monthlyData[$monthly]['bill'] = KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', (int)$data['date']]])->orderBy('day', 'desc')->value('bill');
                        $this->monthlyData[$monthly]['paid'] = KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', (int)$data['date']]])->orderBy('day', 'desc')->value('isPaid');
                    }
                }
                break;
            case 3:
                // dd($value);
                $this->dailyData = [];
                $this->hourlyData = [];
                $this->selectedDay = 0;
                if($value != 0){
                    $this->selectedMonth = $value;
                }

                $lastDay = (int)date("t", strtotime($this->selectedYear .'-'. $this->selectedMonth));

                $manyDay = [];
                foreach(KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', $this->selectedMonth]])->orderBy('day', 'asc')->get('day') as $data){
                    if($data->day != $lastDay+1){
                        if(!in_array($data->day, $manyDay)){
                            $manyDay[] = $data->day;
                        }
                    }
                }
                // dd($manyDay);

                if($manyDay){
                    foreach($manyDay as $day => $data){
                        $this->dailyData[$day] = [
                            'date' => date_format(date_create($this->selectedYear .'-'. $this->selectedMonth .'-'. $data), 'd/m/Y'),
                            'tariff' => KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', $this->selectedMonth], ['day', $data]])->orderBy('hour', 'desc')->value('tariff'),
                        ];
                    }
                    // dd($this->monthlyData[$month]);
                    foreach($this->dailyData as $daily => $data){
                        // $a_date = $this->selectedYear.'-'.(int)$data['date'];
                        // $lastMonth = (int)date("t", strtotime($a_date));

                        $this->dailyData[$daily]['kwh'] = KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', $this->selectedMonth], ['day', (int)$data['date']]])->orderBy('hour', 'desc')->value('kwh');
                        $this->dailyData[$daily]['bill'] = KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', $this->selectedMonth], ['day', (int)$data['date']]])->orderBy('hour', 'desc')->value('bill');
                    }
                }
                // dd($this->dailyData);
                break;
            case 4:
                $this->hourlyData = [];
                if($value != 0){
                    $this->selectedDay = $value;
                }
                // $lastDay = (int)date("t", strtotime($this->selectedYear .'-'. $this->selectedMonth));

                $manyHour = [];
                foreach(KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', $this->selectedMonth], ['day', $this->selectedDay]])->orderBy('hour', 'asc')->get('hour') as $data){
                    if(!in_array($data->hour, $manyHour)){
                        $manyHour[] = $data->hour;
                    }
                }
                // dd($manyHour);

                if($manyHour){
                    // $hourStart = $manyHour[0];
                    // $hourEnd = $manyHour[count($manyHour)-1];
                    foreach($manyHour as $hour => $data){
                        if($data == 24){
                            $this->hourlyData[$hour]['date'] = date_format(date_create($this->selectedYear .'-'. $this->selectedMonth .'-'. $this->selectedDay), 'd/m/Y') .' '. $data .':00';
                        } else{
                            $this->hourlyData[$hour]['date'] = date_format(date_create($this->selectedYear .'-'. $this->selectedMonth .'-'. $this->selectedDay .' '. $data .':0'), 'd/m/Y H:i');
                        }
                        $tariff = KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', $this->selectedMonth], ['day', $this->selectedDay]])->orderBy('hour', 'desc')->value('tariff');
                        if($tariff){
                            $this->hourlyData[$hour]['tariff'] = $tariff;
                        } else{
                            $this->hourlyData[$hour]['tariff'] = $this->hourlyData[$hour-1]['tariff'];
                        }
                    }

                    // dd($this->hourlyData);
                    foreach($this->hourlyData as $hourly => $data){
                        // dd($manyHour[$hourly]);
                        $this->hourlyData[$hourly]['kwh'] = KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', $this->selectedMonth], ['day', $this->selectedDay], ['hour', $manyHour[$hourly]]])->value('kwh');
                        $this->hourlyData[$hourly]['bill'] = KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', $this->selectedMonth], ['day', $this->selectedDay], ['hour', $manyHour[$hourly]]])->value('bill');
                    }
                }
                break;
            default:
                break;
        }
    }

    public function verifyBill($month){
        // dd($month);
        $this->selectedBill = $month;
    }

    public function confirmPayment(){
        KwhHistory::where([['roomId', $this->selectedRoom], ['userId', $this->selectedUser], ['year', $this->selectedYear], ['month', $this->selectedBill]])->update(['isPaid' => 1]);
        $this->billDetail($this->selectedYear, 2);
        session()->flash('message', 'Berhasil verifikasi pembayaran');
    }
}
