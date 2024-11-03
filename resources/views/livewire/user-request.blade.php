<div class="mb-3 p-3 bg-white shadow-sm rounded">
    <div class="container"></div>
    @if (session()->has('success'))
        <div class="pt-3">
            <div class="alert alert-success">
                {{session('success')}}
            </div>
        </div>
    @elseif (session()->has('error'))
        <div class="pt-3">
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
        </div>
    @endif
    <div class="p-3">
        <h3>Permintaan Penghunian</h3>
    </div>
    @if ($viewRoom == true)
        <h5 class="ml-2 row">
            <label class="col-sm-1">Kos</label>
            <div class="col">
                : {{$kosData[$selectedKos]['name']}}
            </div>
        </h5>
        <div class="p-3">
            <button wire:click="viewRequest(0)" class="btn btn-secondary">Kembali</button>
        </div>
    @endif
    <table class="table table-hover">
        @if ($viewRoom == false)
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Kos</th>
                    <th scope="col">Alamat</th>
                    <th scope="col">Permintaan</th>
                    <th scope="col">Lihat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kosData as $kos => $data)
                    <tr>
                        <td scope="row">{{$kos+1}}</td>
                        <td scope="row">{{$data['name']}}</td>
                        <td scope="row">{{$data['address']}}</td>
                        <td scope="row">{{$data['count']}}</td>
                        <td scope="row">
                            @if ($data['count'] > 0)
                                <button wire:click="viewRequest({{$kos}})" class="btn btn-sm btn-link">
                                    <i class="bi bi-eye-fill">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                        </svg>
                                    </i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        @else
            <thead>
                <tr>
                    {{-- <th scope="col">No</th> --}}
                    <th scope="col">Kamar</th>
                    <th scope="col">Daya</th>
                    <th scope="col">Tariff</th>
                    <th scope="col">Nama Calon</th>
                    <th scope="col">Verifikasi</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roomData[$selectedKos] as $request => $data)
                    @foreach ($userRequest[$selectedKos][$request] as $item => $value)
                        <tr>
                            {{-- <td>{{$item+1}}</td> --}}
                            <td>{{$data['name']}}</td>
                            <td>{{$data['power']}}</td>
                            <td>{{$data['tariff']}}</td>
                            <td>{{$value['userName']}}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" disabled>Belum</button>
                            </td>
                            <td>
                                <div class="p-1">
                                    <a wire:click="verifyRequest({{$value['userId']}}, {{$data['id']}})" role="button" class="btn btn-sm btn-link" data-toggle="modal" data-target="#staticBackdropConfirm">
                                        <i class="bi bi-check2-square">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-square" viewBox="0 0 16 16">
                                                <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z"/>
                                                <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0"/>
                                            </svg>
                                        </i>
                                    </a>
                                    <div wire:ignore.self class="modal fade" id="staticBackdropConfirm" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="staticBackdropLabel">Terima Permintaan</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Yakin ingin menerima permintaan ini?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button wire:click="confirmAction(true)" type="button" class="btn btn-primary" data-dismiss="modal">Yakin</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <a wire:click="verifyRequest({{$value['userId']}}, {{$data['id']}})" role="button" class="badge badge-danger" data-toggle="modal" data-target="#staticBackdrop">
                                        <i class="bi bi-ban">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ban" viewBox="0 0 16 16">
                                                <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                            </svg>
                                        </i>
                                    </a>
                                    <div wire:ignore.self class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="staticBackdropLabel">Tolak Permintaan</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Yakin ingin menolak permintaan ini?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button wire:click="confirmAction(null)" type="button" class="btn btn-primary" data-dismiss="modal">Yakin</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        @endif
    </table>
</div>
