<div class="container-fluid h-100 inline-block">
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
    @elseif (session()->has('error'))
        <div class="pt-3">
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
        </div>
    @endif
    <div class="mb-3 p-3 bg-white shadow-sm rounded">
        <div>
            @if ($seeRoom == false)
                @if ($isAdding == false && $isEditing == false)
                    <button type="button" class="btn btn-link" disabled>
                        <h2 class="text-dark">Data Kos</h2>
                    </button>
                    <button wire:click="addKos(true)" type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Tambah Kos">+</button>
                @elseif($isAdding == true)
                    <button type="button" class="btn btn-link" disabled>
                        <h2 class="text-dark">Tambah Kos</h2>
                    </button>
                @elseif($isEditing == true)
                    <button type="button" class="btn btn-link" disabled>
                        <h2 class="text-dark">Edit Kos</h2>
                    </button>
                @endif
            @else
                @if ($isAdding == false && $isEditing == false)
                    <button type="button" class="btn btn-link" disabled>
                        <h2 class="text-dark">Data Kamar {{$kosName}}</h2>
                    </button>
                    <button wire:click="addKos(true)" type="button" class="btn btn-primary"  data-toggle="tooltip" data-placement="top" title="Tambah Kamar">+</button>
                @elseif($isAdding == true)
                    <button type="button" class="btn btn-link" disabled>
                        <h2 class="text-dark">Tambah Kamar {{$kosName}}</h2>
                    </button>
                @elseif($isEditing == true)
                    <button type="button" class="btn btn-link" disabled>
                        <h2 class="text-dark">Edit Kamar {{$kosName}}</h2>
                    </button>
                @endif
            @endif
        </div>

        @if ($isAdding == true || $isEditing == true)
            <form action="">
                @csrf
                @if ($seeRoom == false)
                    <div class="m-3 row">
                        <label for="name" class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" wire:model="name">
                        </div>
                    </div>
                    <div class="m-3 row">
                        <label for="address" class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" wire:model="address">
                        </div>
                    </div>
                    <div class="m-3 row">
                        <div wire:loading.attr="disabled" class="col-sm">
                            <button type="button" class="btn btn-primary" name="submit"
                                @if ($isAdding == true)
                                    wire:click="store()"
                                @elseif($isEditing == true)
                                    wire:click="update()"
                                @endif
                            >Simpan</button>
                            <button type="button" class="btn btn-danger" name="submit"
                                @if ($isAdding == true)
                                    wire:click="addKos(false)"
                                @elseif($isEditing == true)
                                    wire:click="clearInput()"
                                @endif
                            >Batal</button>
                        </div>
                    </div>
                {{-- </form> --}}
                @else
                {{-- <form action=""> --}}
                    <div class="m-3 row">
                        <label for="name" class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" wire:model="name">
                        </div>
                    </div>
                    <div class="m-3 row">
                        <label for="address" class="col-sm-2 col-form-label">Daya (VA)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" wire:model="power">
                        </div>
                    </div>
                    <div class="m-3 row">
                        <label for="address" class="col-sm-2 col-form-label">Tarif / KWh (Rp)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" wire:model="tariff">
                        </div>
                    </div>
                    <div class="m-3 row">
                        <label for="address" class="col-sm-2 col-form-label">Waktu</label>
                        <div class="col-sm-10">
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                    @switch($timezone)
                                        @case(7)
                                            WIB
                                            @break
                                        @case(8)
                                            WITA
                                            @break
                                        @case(9)
                                            WIT
                                            @break
                                        @default
                                            @break
                                    @endswitch
                                </button>
                                <div class="dropdown-menu">
                                    <a wire:click="selectTimezone(7)" class="dropdown-item" href="#">WIB</a>
                                    <a wire:click="selectTimezone(8)" class="dropdown-item" href="#">WITA</a>
                                    <a wire:click="selectTimezone(9)" class="dropdown-item" href="#">WIT</a>
                                </div>
                              </div>
                            {{-- <input type="text" class="form-control" wire:model="timezone"> --}}
                        </div>
                    </div>
                    <div class="m-3 row">
                        <div wire:loading.attr="disabled" class="col-sm">
                            <button type="button" class="btn btn-primary" name="submit"
                                @if ($isAdding == true)
                                    wire:click="store()"
                                @elseif($isEditing == true)
                                    wire:click="update()"
                                @endif
                            >Simpan</button>
                            <button type="button" class="btn btn-danger" name="submit"
                                @if ($isAdding == true)
                                    wire:click="addKos(false)"
                                @elseif($isEditing == true)
                                    wire:click="clearInput()"
                                @endif
                            >Batal</button>
                        </div>
                    </div>
                @endif
            </form>
        @endif
        @if ($seeRoom == true)
            <div wire:loading.attr="disabled" class="row p-3">
                <button wire:click="getRoom(0, false)" type="button" class="btn btn-secondary">Back</button>
            </div>
        @endif
        <div wire:loading class="row p-3">
            <h5>Harap Tunggu</h5>
        </div>
        {{-- <div class="pb-3 pt-3">
            <input type="text" class="form-control mb-3 w-25" placeholder="Cari" wire:model.live="keyword">
        </div> --}}
        <table class="table table-hover">
        @if ($seeRoom == false)
            <thead>
                <tr>
                    {{-- <th scope="col"></th> --}}
                    <th scope="col">No</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Alamat</th>
                    {{-- <th scope="col">Tagihan</th> --}}
                    <th wire:loading.attr="disabled" scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kosData as $data => $value)
                {{-- {{$isEditing}} --}}
                    <tr>
                        {{-- <td><input type="checkbox" wire:key="{{$value->id}}" value="{{$value->id}}" wire:model.live="selectedData"></td> --}}
                        <td>{{$kosData->firstItem() + $data}}</td>
                        <td>{{$value->name}}</td>
                        <td>{{$value->address}}</td>
                        {{-- <td></td> --}}
                        <td wire:loading.attr="disabled">
                            <a wire:click="getRoom({{$value->id}}, true)" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Lihat Kos">
                                <i class="bi bi-eye-fill">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                    </svg>
                                </i>
                            </a>
                            <a wire:click="edit({{$value->id}})" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="Edit Kos">
                                <i class="bi bi-pencil-fill">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                    </svg>
                                </i>
                            </a>
                            <button wire:click="confirmDelete({{$value->id}})" type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#staticBackdrop">
                                <i class="bi bi-trash-fill" data-toggle="tooltip" data-placement="top" title="Hapus Kos">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                        <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                                    </svg>
                                </i>
                            </button>
                            {{-- <a wire:click="confirmDelete({{$value->id}})" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                <i class="bi bi-trash-fill">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                        <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                                    </svg>
                                </i>
                            </a> --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @else
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Daya (VA)</th>
                    <th scope="col">Tarif/KWh (Rp)</th>
                    <th scope="col">Waktu</th>
                    <th scope="col">
                        Perangkat IoT
                        <i class="bi bi-info-circle-fill" data-toggle="modal" data-target="#staticBackdropInfo">
                            <span class="btn btn-sm btn-link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2"/>
                                </svg>
                            </span>
                        </i>
                        <div class="modal fade" id="staticBackdropInfo" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabel">Pin GPIO Sensor</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <label class="col-sm-2">LDR</label>
                                            <div class="col">
                                                : 34
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-sm-2">PIR</label>
                                            <div class="col">
                                                : 19
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-sm-2">DHT11</label>
                                            <div class="col">
                                                : 23
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-sm-2">PZEM</label>
                                            <div class="col">
                                                : 16 & 17
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-sm-2">Relay</label>
                                            <div class="col">
                                                : 21, 22, 25, 26, 32, 33
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-sm-2">Saklar</label>
                                            <div class="col">
                                                : 13, 14, 15, 18, 27, 35
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th scope="col">Penghuni</th>
                    <th wire:loading.attr="disabled" scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if ($roomData === null)
                    {{-- <h3 class="text-center">Data Kosong</h3> --}}
                @else
                    @foreach ($roomData as $room => $value)
                    {{-- {{$isEditing}} --}}
                        <tr>
                            {{-- <td><input type="checkbox" wire:key="{{$value->id}}" value="{{$value->id}}" wire:model.live="selectedData"></td> --}}
                            <td>{{$room+1}}</td>
                            <td>{{$value->name}}</td>
                            <td>{{$value->power}}</td>
                            <td>{{$value->tariff}}</td>
                            @switch($value->timezone)
                                @case(7)
                                    <td>WIB</td>
                                    @break
                                @case(8)
                                    <td>WITA</td>
                                    @break
                                @case(9)
                                    <td>WIT</td>
                                    @break
                                @default
                                    @break
                            @endswitch
                            @if ($roomDevice[$room])
                                <td>
                                    Tertaut
                                    {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">Open modal for @mdo</button> --}}
                                    <a wire:click="setRoomId({{$value->id}})" type="button" class="btn btn-link btn-sm" data-toggle="modal" data-target="#wifiModal">
                                        <span data-toggle="tooltip" data-placement="top" title="Download Sketch">
                                            <i class="bi bi-cloud-arrow-down-fill">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-arrow-down-fill" viewBox="0 0 16 16">
                                                    <path d="M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2m2.354 6.854-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 9.293V5.5a.5.5 0 0 1 1 0v3.793l1.146-1.147a.5.5 0 0 1 .708.708"/>
                                                </svg>
                                            </i>
                                        </span>
                                    </a>
                                </td>
                            @else
                            <td>
                                <a wire:click="createDevice({{$value->id}})" href="#" type="button" class="btn btn-danger btn-sm">
                                    Tautkan
                                </a>
                            </td>
                            @endif
                            @if ($roomUser[$room])
                                <td>
                                    {{$roomUser[$room]}}
                                    <button wire:click="setRoomId({{$value->id}})" class="btn btn-sm btn-outline-danger" role="button" data-toggle="modal" data-target="#deleteUserModal">
                                        <i class="bi bi-ban">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ban" viewBox="0 0 16 16">
                                                <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                            </svg>
                                        </i>
                                    </button>
                                    <div wire:ignore.self class="modal fade" id="deleteUserModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="staticBackdropLabel">Hapus Kos</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin menghapus penghuni ini?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-backdrop="static" data-dismiss="modal" wire:click="clearInput()"">Batal</button>
                                                    <button wire:click="deleteUser()" type="button" class="btn btn-primary" data-dismiss="modal">Yakin</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @else
                                <td>-</td>
                            @endif

                            {{-- <td></td> --}}
                            <td wire:loading.attr="disabled">
                                <a wire:click="edit({{$value->id}})" class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="Edit Kamar">
                                    <i class="bi bi-pencil-fill">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                        </svg>
                                    </i>
                                </a>
                                <button wire:click="confirmDelete({{$value->id}})" type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#staticBackdrop">
                                    <i class="bi bi-trash-fill"  data-toggle="tooltip" data-placement="top" title="Hapus Kamar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                            <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                                        </svg>
                                    </i>
                                </button>
                                {{-- <a wire:click="confirmDelete({{$value->id}})" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                    <i class="bi bi-trash-fill">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                            <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                                        </svg>
                                    </i>
                                </a> --}}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        @endif
        </table>
        <!-- Button trigger modal -->

  <!-- Modal -->
        <div wire:ignore.self class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Hapus Kos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    @if ($seeRoom == false)
                        Apakah Anda yakin ingin menghapus Kos ini?
                    @else
                        Apakah Anda yakin ingin menghapus Kamar ini?
                    @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-backdrop="static" data-dismiss="modal" wire:click="clearInput()"">Batal</button>
                        <button wire:click="delete()" type="button" class="btn btn-primary" data-dismiss="modal">Yakin</button>
                    </div>
                </div>
            </div>
        </div>
        <div wire:ignore.self class="modal fade" id="wifiModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">WiFi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">SSID</label>
                                <input type="text" class="form-control" id="recipient-name" wire:model="wifiSsid">
                            </div>
                                <div class="form-group">
                                <label for="message-text" class="col-form-label">Password</label>
                                <input type="password" class="form-control" id="message-text" wire:model="wifiPass"></input>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button wire:click="donwloadSketch()" type="button" class="btn btn-primary" data-dismiss="modal">Download</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
