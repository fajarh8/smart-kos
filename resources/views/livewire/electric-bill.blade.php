<div class="container-fluid h-100">
    @if (session()->has('message'))
        <div class="pt-3">
            <div class="alert alert-success">
                {{session('message')}}
            </div>
        </div>
    @elseif (session()->has('error'))
        <div class="pt-3">
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
        </div>
    @endif
    <div class="mb-3 p-3 bg-white shadow-sm rounded">
        <div class="p-3">
            <h2>
                Data Tagihan Listrik
            </h2>
            @if ($getDetail == true)
                {{-- <table class="table table-borderless">
                    <thead>
                    <tr>
                        <th scope="col">Kos</th>
                        <th scope="col">First</th>
                        <th scope="col">Last</th>
                        <th scope="col">Handle</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Mark</td>
                        <td>Otto</td>
                        <td>@mdo</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Jacob</td>
                        <td>Thornton</td>
                        <td>@fat</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td colspan="2">Larry the Bird</td>
                        <td>@twitter</td>
                    </tr>
                    </tbody>
                </table> --}}
                <div class="mt-3 row">
                    <label class="col-sm-1"><h5>Kos</h5></label>
                    <div class="col">
                        <h5>: {{$kosData[array_search($selectedKos, array_column($kosData, 'id'))]['name']}}</h5>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-1"><h5>Kamar</h5></label>
                    <div class="col">
                        <h5>: {{$roomData[array_search($selectedKos, array_column($kosData, 'id'))][array_search($selectedRoom, array_column($roomData[array_search($selectedKos, array_column($kosData, 'id'))], 'id'))]['name']}}</h5>
                    </div>
                </div>
                @if ($mode > 0)
                    <div class="row">
                        <label class="col-sm-1"><h5>Penghuni</h5></label>
                        <div class="col">
                            <h5>: {{$roomUser[array_search($selectedUser, array_column($roomUser, 'userId'))]['userName']}}</h5>
                        </div>
                    </div>
                @endif
            @endif
        </div>
        <div class="row pl-4">
            <h5 wire:loading>Harap tunggu...</h5>
        </div>
        <table class="table table-hover">
            @if ($getDetail == false)
                <thead>
                    <th scope="col">No</th>
                    <th scope="col">Nama Kos</th>
                    <th scope="col">Alamat</th>
                    <th scope="col">Kamar</th>
                </thead>
                <tbody>
                    @foreach ($kosData as $kos => $value)
                        <tr>
                            <td>{{$kos+1}}</td>
                            <td>{{$value['name']}}</td>
                            <td>{{$value['address']}}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        Kamar
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @if(array_key_exists($kos, $roomData))
                                            @foreach ($roomData[$kos] as $room => $data)
                                                <button wire:click="getRoom({{$data['id']}}, true, {{$value['id']}})" type="button" class="dropdown-item">
                                                    @if ($data['userId'] !== null)
                                                        {{$data['name'] ." (". $data['userName'] .")"}}
                                                    @else
                                                        {{$data['name'] ." (Kosong)"}}
                                                    @endif
                                                </button>
                                            @endforeach
                                        @else
                                            Belum ada kamar
                                        @endif
                                        {{-- @foreach ($roomName[$kos] as $room => $name)
                                            <button wire:click="getRoom({{$room}}, true, {{$kos}})" type="button" class="dropdown-item">
                                                @if ($currentUser[$kos][$room] !== null)
                                                    {{$name ." (". $currentUser[$kos][$room] .")"}}
                                                @else
                                                    {{$name ." (Kosong)"}}
                                                @endif
                                            </button>
                                        @endforeach --}}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @elseif($getDetail == true)
                <div class="row p-3">
                    <button
                    @if ($mode == 0)
                        wire:click="getRoom(0, false, 0)"
                    @elseif ($mode == 1)
                        wire:click="billDetail(0, 0)"
                    @elseif ($mode == 2)
                        wire:click="billDetail(0, 1)"
                    @elseif ($mode == 3)
                        wire:click="billDetail(0, 2)"
                    @elseif ($mode == 4)
                        wire:click="billDetail(0, 3)"
                    @endif
                    type="button" class="btn btn-secondary"
                >Kembali</button>
                </div>
                <thead>
                    <th scope="col">No</th>
                    @if ($mode == 0)
                        <th scope="col">Penghuni</th>
                    @endif
                    <th scope="col">Waktu</th>
                    @if ($mode > 0)
                        <th scope="col" class="col-md-2 text-left">Tarif</th>
                        <th scope="col" class="col-md-2 text-left">KWh</th>
                        <th scope="col" class="col-md-2 text-left">Tagihan</th>
                    @endif
                    @if ($mode == 2)
                        <th scope="col" class="col-md-2 text-left">Status</th>
                    @endif
                    @if ($mode != 4)
                        <th scope="col"></th>
                    @endif
                </thead>
                <tbody>
                    @if($mode == 0)
                        @if ($roomUser)
                            @foreach ($roomUser as $room => $value)
                                <tr>
                                    <td>{{$room+1}}</td>
                                    <td>{{$value['userName']}}</td>
                                    <td>
                                        @if ($value['yearStart'] == $value['yearEnd'])
                                            {{$value['yearStart']}}
                                        @else
                                            {{$value['yearStart'] .' - '. $value['yearEnd']}}
                                        @endif
                                    </td>
                                    <td>
                                        <button wire:click="billDetail({{$value['userId']}}, 1)" wire:loading.attribute="disabled" type="button" class="btn btn-link btn-sm">
                                            <i class="bi bi-eye-fill">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                                </svg>
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @elseif($mode == 1)
                        @foreach ($yearlyData as $index => $data)
                            <tr>
                                <td>{{$index+1}}</td>
                                <td>
                                    {{$data['date']}}
                                </td>
                                <td>Rp {{number_format($data['tariff'], 2, ',', '.')}}</td>
                                <td>{{floatval($data['kwh']) .' KWh'}}</td>
                                <td>
                                    @if ($data['bill'] > 0)
                                        <button wire:click="billDetail({{(int)$data['date']}}, 2)" type="button" class="btn btn-outline-danger btn-sm">{{$data['bill'] ." Tunggakan"}}</button>
                                    @else
                                        <button type="button" class="btn btn-outline-success btn-sm" disabled>Lunas</button>
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="billDetail({{(int)$data['date']}}, 2)" wire:loading.attribute="disabled" type="button" class="btn btn-link btn-sm">
                                        <i class="bi bi-eye-fill">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                            </svg>
                                        </i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @elseif($mode == 2)
                        @foreach ($monthlyData as $index => $data)
                            <tr>
                                <td>{{$index+1}}</td>
                                <td>
                                    {{$data['date']}}
                                </td>
                                <td>Rp {{number_format($data['tariff'], 2, ',', '.')}}</td>
                                <td>{{floatval($data['kwh']) .' KWh'}}</td>
                                <td>
                                    Rp {{number_format($data['bill'], 2, ',', '.')}}
                                </td>
                                <td>
                                    @if (date_format(date_create($currentYear .'-'. $currentMonth), 'm/Y') == $data['date'])
                                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled>Dalam Proses</button>
                                    @else
                                        @if ($data['paid'] == 0)
                                            <button wire:click="verifyBill({{(int)$data['date']}})" wire:loading.attribute="disabled" type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#staticBackdrop">Menunggak</button>
                                            <button wire:click="verifyBill({{(int)$data['date']}})" wire:loading.attribute="disabled" type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#staticBackdrop">
                                                <i class="bi bi-patch-check-fill">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-patch-check-fill" viewBox="0 0 16 16">
                                                        <path d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01zm.287 5.984-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708.708"/>
                                                    </svg>
                                                </i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-success btn-sm" disabled>Lunas</button>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="billDetail({{(int)$data['date']}}, 3)" wire:loading.attribute="disabled" type="button" class="btn btn-link btn-sm">
                                        <i class="bi bi-eye-fill">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                            </svg>
                                        </i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @elseif ($mode == 3)
                        @foreach ($dailyData as $index => $data)
                            <tr>
                                <td>{{$index+1}}</td>
                                <td>
                                    {{$data['date']}}
                                </td>
                                <td>Rp {{number_format($data['tariff'], 2, ',', '.')}}</td>
                                <td>{{floatval($data['kwh']) .' KWh'}}</td>
                                <td>
                                    Rp {{number_format($data['bill'], 2, ',', '.')}}
                                </td>
                                <td>
                                    <button wire:click="billDetail({{(int)$data['date']}}, 4)" wire:loading.attribute="disabled" type="button" class="btn btn-link btn-sm">
                                        <i class="bi bi-eye-fill">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                            </svg>
                                        </i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @elseif ($mode == 4)
                        @foreach ($hourlyData as $index => $data)
                            <tr>
                                <td>{{$index+1}}</td>
                                <td>
                                    {{$data['date']}}
                                </td>
                                <td>Rp {{number_format($data['tariff'], 2, ',', '.')}}</td>
                                <td>{{floatval($data['kwh']) .' KWh'}}</td>
                                <td>
                                    Rp {{number_format($data['bill'], 2, ',', '.')}}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            @endif
        </table>
            <div wire:ignore.self class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Konfirmasi Pembayaran</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Konfirmasi pembayaran listrik ini?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-backdrop="static" data-dismiss="modal">Batal</button>
                            <button wire:click="confirmPayment()" type="button" class="btn btn-primary" data-dismiss="modal">Yakin</button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
