<div class="container h-100 w-100">
    @if ($deviceConnected == true)
        <div class="card shadow mb-4">
            <div class="p-4">
                <h2>Relay</h2>
                <table class="table table-lg table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="col-md-0 text-center">No.</th>
                            <th scope="col" class="col-md-5 text-left">Nama Perangkat</th>
                            <th scope="col" class="col-md-1 text-center">Automasi</th>
                            <th scope="col" class="col-md-1 text-center">PIR</th>
                            <th scope="col" class="col-md-1 text-center">Jenis</th>
                            <th scope="col" class="col-md-2 text-center">ON</th>
                            <th scope="col" class="col-md-2 text-center">OFF</th>
                            <th scope="col" class="col-md-1 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i=0; $i<6; $i++)
                            <tr id="hoverMe">
                                <td class="text-center">{{$i + 1}}</td>
                                <td class="text-left">
                                    {{$label[$i]}}
                                </td>
                                <td class="text-center">
                                    @if ($type[$i] != null)
                                        <label class="switch mt-2" wire:click="autoSwitch({{$i}})">
                                            <input wire:click="autoSwitch({{$i}})" type="checkbox" {{$automation[$i] ? "checked" : ""}}>
                                            <span class="slider round"></span>
                                        </label>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <label class="switch mt-2">
                                        <input wire:click="pirSwitch({{$i}})" type="checkbox" {{$pirAuto[$i] ? "checked" : ""}}>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    @switch($type[$i])
                                        @case(2)
                                            Suhu
                                            @break
                                        @case(3)
                                            Cahaya
                                            @break
                                        @case(1)
                                            Waktu
                                            @break
                                        @default
                                    @endswitch
                                </td>
                                <td class="text-center">
                                    @if ($type[$i] != 2 && $type[$i] != 3)
                                        {{$onTime[$i]}}
                                    @elseif ($type[$i] == 2)
                                        >= {{$tempThreshold[$i]}}°C
                                    @elseif ($type[$i] == 3)
                                        < {{$ldrThreshold[$i]}}%
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($type[$i] != 2 && $type[$i] != 3)
                                        {{$offTime[$i]}}
                                    @elseif ($type[$i] == 2)
                                        < {{$tempThreshold[$i]}}°C
                                    @elseif ($type[$i] == 3)
                                        >= {{$ldrThreshold[$i]}}%
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button id="hideMe" wire:click="selectRelay({{$i}})" type="button" class="btn btn-primary">Edit</button>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
        @if ($errors->any())
            <div class="pt-3 alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session()->has('message'))
            <div class="pt-3">
                <div class="alert alert-success">
                    {{session('message')}}
                </div>
            </div>
        @elseif (session()->has('alert'))
            <div class="pt-3">
                <div class="alert alert-danger">
                    {{session('alert')}}
                </div>
            </div>
        @endif
        <div class="card shadow mb-4">
            <div class="p-4">
                <form>
                    <div class="mb-3 row">
                        <label for="relayNumber" class="col-sm-2 col-form-label"><h2>Edit Relay</h2></label>
                        <div class="col-sm-1">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$selectedRelay}}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                @for ($i = 0; $i < 6; $i++)
                                    <a wire:click="selectRelay({{$i}})" class="dropdown-item" href="#">{{$i+1}}</a>
                                @endfor
                            </div>
                            <input type="number" wire:model="selectedRelay" hidden required>
                        </div>
                    </div>
                    @if ($selectedRelay != null)
                        <div class="mb-3 row">
                            <label for="relayLabel" class="col-sm-2 col-form-label">Nama Perangkat</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" wire:model="selectedLabel" required>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="automationType" class="col-sm-2 col-form-label">Jenis</label>
                            <div class="col-sm-1">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    @switch($selectedType)
                                        @case(2)
                                            Suhu
                                            @break
                                        @case(3)
                                            Cahaya
                                            @break
                                        @case(1)
                                            Waktu
                                            @break
                                        @default
                                    @endswitch
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a wire:click="selectType(2)" class="dropdown-item" href="#">Suhu</a>
                                    <a wire:click="selectType(3)" class="dropdown-item" href="#">Cahaya</a>
                                    <a wire:click="selectType(1)" class="dropdown-item" href="#">Waktu</a>
                                </div>
                                <input type="text" wire:model="selectedType" hidden required>
                            </div>
                        </div>
                        @if ($selectedType == 2)
                            <div class="mb-3 row">
                                <label for="relayTempThreshold" class="col-sm-2 col-form-label">Batas</label>
                                <div class="col-sm-1">
                                    <input type="text" class="text-center form-control" wire:model="tempThresholdInput" required>
                                </div>
                                <div>
                                    °C
                                </div>
                            </div>
                        @elseif ($selectedType == 3)
                            <div class="mb-3 row">
                                <label for="ldrThresholdInput" class="col-sm-2 col-form-label">Batas</label>
                                <div class="col-sm-1">
                                    <input type="text" class="text-center form-control" wire:model="ldrThresholdInput" required>
                                </div>
                                <div class="col-sm-1">
                                    %
                                </div>
                            </div>
                        @elseif ($selectedType == 1)
                            <div class="mb-3 row">
                                <label for="relayOnHour" class="col-sm-2 col-form-label">ON</label>
                                <div class="col-sm-1">
                                    <input type="text" class="text-center form-control" wire:model="onHourInput" required>
                                </div>
                                <div class="font-weight-bold">:</div>
                                <div class="col-sm-1">
                                    <input type="text" class="text-center form-control" wire:model="onMinuteInput" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="relayOffHour" class="col-sm-2 col-form-label">OFF</label>
                                <div class="col-sm-1">
                                    <input type="text" class="text-center form-control" wire:model="offHourInput" required>
                                </div>
                                <div class="font-weight-bold">:</div>
                                <div class="col-sm-1">
                                    <input type="text" class="text-center form-control" wire:model="offMinuteInput" required>
                                </div>
                            </div>
                        @endif
                        @if ($selectedType != null)
                            <div class="row">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <button type="button" class="btn btn-primary" name="submit" wire:click="editAutomation({{$selectedRelay}})">UPDATE</button>
                                </div>
                            </div>
                        @endif
                    @endif
                </form>
                <br>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="p-4">
                @if ($editPir != true)
                    <h2>PIR</h2>
                @else
                    <h2>Edit PIR</h2>
                @endif
                <form>
                    @if ($editPir == true)
                        <div class="mb-1 row">
                            <label for="relayOnHour" class="col-sm-2 col-form-label">Interval</label>
                            <div class="col-sm-1">
                                <input type="text" class="text-center form-control" wire:model="pirIntervalInput" required>
                            </div>
                        </div>
                    @else
                        <div class="mb-1 row">
                            <label for="relayOnHour" class="col-sm-2 col-form-label">Interval</label>
                            <div class="col-sm-1">
                                {{$pirInterval}}
                            </div>
                        </div>
                    @endif
                    <div class="mb-1 row">
                        <label for="name" class="col-sm-2 col-form-label">Jadwal PIR</label>
                        <div class="col-sm-1">
                            @if ($pirOnMinute !== null)
                                <label class="switch mt-2">
                                    <input id="pirScheduleSwitch" type="checkbox" wire:click="selectPir()" {{$pirOrigin ? "checked" : ""}}>
                                    <span class="slider round"></span>
                                </label>
                            @endif
                        </div>
                    </div>
                    @if ($editPir == true)
                        <div class="mb-1 row">
                            <label for="relayOnHour" class="col-sm-2 col-form-label">ON</label>
                            <div class="col-sm-1">
                                <input type="text" class="text-center form-control" wire:model="pirOnHourInput" required>
                            </div>
                            <div class="font-weight-bold">:</div>
                            <div class="col-sm-1">
                                <input type="text" class="text-center form-control" wire:model="pirOnMinInput" required>
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <label for="relayOffHour" class="col-sm-2 col-form-label">OFF</label>
                            <div class="col-sm-1">
                                <input type="text" class="text-center form-control" wire:model="pirOffHourInput" required>
                            </div>
                            <div class="font-weight-bold">:</div>
                            <div class="col-sm-1">
                                <input type="text" class="text-center form-control" wire:model="pirOffMinInput" required>
                            </div>
                        </div>
                    @else
                        <div class="mb-1 row">
                            <label for="relayOnHour" class="col-sm-2 col-form-label">ON</label>
                            <div class="col-sm-1">
                                {{$pirOn}}
                            </div>
                        </div>
                        <div class="mb-1 row">
                            <label for="relayOffHour" class="col-sm-2 col-form-label">OFF</label>
                            <div class="col-sm-1">
                                {{$pirOff}}
                            </div>
                        </div>
                    @endif
                    @if ($editPir != true)
                        <div class="mb-1 row">
                            <div class="mr-3">
                                <button type="button" class="btn btn-primary" name="submit" wire:click="editingPir(1)">EDIT</button>
                            </div>
                        </div>
                    @else
                        <div class="mb-1 row">
                            <div class="mr-3">
                                <button type="button" class="btn btn-primary" name="submit" wire:click="storePir()">SIMPAN</button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger" name="submit" wire:click="editingPir(0)">BATAL</button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    @else
    <div>Device belum terhubung, silakan hubungi admin</div>
    @endif
</div>
