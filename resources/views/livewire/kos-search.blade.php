<div class="w-100 h-100">
    <div class="container">
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
    </div>
    {{-- @if ($getRoom == false) --}}
        <div class="container mb-4 bg-white shadow-sm rounded">
            <div class="p-3">
                <h2>
                    @if ($requestData['isVerified'] == 0)
                        Permintaan Saat Ini
                    @else
                        Kos Saat Ini
                    @endif
                </h2>
                @if ($anyRequest == true)
                    <div class="mt-3 row">
                        <label class="col-sm-1">Kos</label>
                        <div class="col">
                            : {{$requestData['kosName']}}
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-1">Kamar</label>
                        <div class="col">
                            : {{$requestData['roomName']}}
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-1">Alamat</label>
                        <div class="col">
                            : {{$requestData['kosAddress']}}
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-1">Admin</label>
                        <div class="col">
                            : {{$requestData['adminName']}}
                        </div>
                    </div>
                    @if ($roomDetail == true)
                        <div class="row">
                            <label class="col-sm-1">Daya</label>
                            <div class="col">
                                : {{$requestData['power']}} VA
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-1">Tarif</label>
                            <div class="col">
                                : Rp {{number_format($requestData['tariff'], 2, ',', '.')}}
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <label class="col-sm-1">Verifikasi</label>
                        <div class="col">
                            :
                            @if ($requestData['isVerified'] == 0)
                                <button type="button" class="btn btn-sm btn-outline-danger" disabled>Belum</button>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-success" disabled>Sudah</button>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3 mb-3 row">
                        @if ($roomDetail == false)
                            <button wire:click="requestDetail()" type="button" class="mr-2 btn btn-primary">
                                Detail
                            </button>
                        @else
                            <button wire:click="requestDetail()" type="button" class="mr-2 btn btn-secondary">
                                Ringkas
                            </button>
                        @endif
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteRequest">
                            Hapus
                        </button>

                        <div class="modal fade" data-backdrop="static" id="deleteRequest" tabindex="-1" aria-labelledby="deleteRequestLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteRequestLabel">Hapus Data</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Anda yakin ingin menghapus data ini?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button wire:click="deleteRequest()" type="button" class="btn btn-primary" data-dismiss="modal">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-3 row">
                        <div class="col">
                            Belum ada permintaan
                        </div>
                    </div>
                @endif
            </div>
        </div>
    {{-- @endif --}}
    <div class="container mb-4 bg-white shadow-sm rounded">
        <div class="p-3">
            <h2 class="mt-3">Cari Kos</h2>
            @if ($getRoom == true)
            <div class="mt-3 row">
                <label class="col-sm-1">Kos</label>
                <div class="col">
                    : {{$emptyKos['name'][$selectedKos]}}
                </div>
            </div>
            <div class="row">
                <label class="col-sm-1">Alamat</label>
                <div class="col">
                    : {{$emptyKos['address'][$selectedKos]}}
                </div>
            </div>
            <div class="row">
                <label class="col-sm-1">Admin</label>
                <div class="col">
                    : {{$emptyKos['adminName'][$selectedKos]}}
                </div>
            </div>
            {{-- <h5 class="row p-1">
                <label for="kosName">Kos</label>
                <p id="kosName">: </p>
            </h5> --}}
            <div class="pt-3">
                <button wire:click="viewRoom(false, 0)" class="btn btn-secondary">Kembali</button>
            </div>
            @endif
        </div>
        <div class="p-3">
            <table class="table table-hover">
                @if ($getRoom == false)
                    <thead>
                        <th scope="row">No</th>
                        <th scope="row">Kos</th>
                        <th scope="row">Alamat</th>
                        <th scope="row">Admin</th>
                        <th scope="row">Kamar Kosong</th>
                        <th scope="row">Lihat</th>
                    </thead>
                    <tbody>
                        @for($i=0; $i<count($emptyKos['name']); $i++)
                            <tr scope="row">
                                {{-- {{$value}} --}}
                                <td scope="col">{{$i+1}}</td>
                                <td scope="col">{{$emptyKos['name'][$i]}}</td>
                                <td scope="col">{{$emptyKos['address'][$i]}}</td>
                                <td scope="col">{{$emptyKos['adminName'][$i]}}</td>
                                <td scope="col">{{$emptyKos['empty'][$i]}} Kamar</td>
                                <td scope="col">
                                    <button wire:click="viewRoom(true, {{$i}})" type="button" class="btn btn-link">
                                        <i class="bi bi-eye-fill">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                            </svg>
                                        </i>
                                    </button>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                @else
                    <thead>
                        <th scope="row">No</th>
                        <th scope="row">Kamar</th>
                        <th scope="row">Daya</th>
                        <th scope="row">Tarif / KWh</th>
                        <th scope="row">Pilih</th>
                    </thead>
                    <tbody>
                        @foreach ($roomIndex as $room => $index)
                            <tr>
                                <td scope="col">{{$room+1}}</td>
                                <td scope="col">{{$emptyRoom[$index]['name']}}</td>
                                <td scope="col">{{$emptyRoom[$index]['power']}} VA</td>
                                <td scope="col">Rp {{number_format($emptyRoom[$index]['tariff'], 2, ',', '.')}}</td>
                                <td scope="col">
                                    <button wire:click="requestRoom({{$emptyRoom[$index]['id']}})" type="button" class="btn btn-link" data-toggle="modal" data-target="#exampleModal">
                                        <i class="bi bi-check2-square">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-square" viewBox="0 0 16 16">
                                                <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z"/>
                                                <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0"/>
                                            </svg>
                                        </i>
                                    </button>
                                    <div wire:ignore.self id="exampleModal" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Pilih Kos</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Anda yakin ingin memilih kamar ini?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button wire:click="confirmSelect()" type="button" class="btn btn-primary" data-dismiss="modal">Iya</button>
                                                </div>
                                            </div>
                                        </div>
                                      </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @endif
            </table>
        </div>
    </div>
</div>
