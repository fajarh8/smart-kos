{{-- Area Chart --}}
<div class="col-xl col-lg">
    <div class="card shadow mb-4">
        <div class="card-body">
            <div>
                <div class="pb-2">
                    <h2>Riwayat</h2>
                </div>
                <div class="pb-2">
                    @if ($mode == 1)
                        <button wire:click="selectMode(0, 0)" type="button" class="btn btn-secondary">Kembali</button>
                    @elseif ($mode == 2)
                        <button wire:click="selectMode(1, {{$selectedYear}})" type="button" class="btn btn-secondary">Kembali</button>
                    @elseif ($mode == 3)
                        <button wire:click="selectMode(2, {{$selectedMonth}})" type="button" class="btn btn-secondary">Kembali</button>
                    @endif
                </div>
                <div class="pb-2">
                    <h5 wire:loading>
                        Memproses data...
                    </h5>
                </div>
            </div>
            <form wire:loading.remove>
                <table class="table table-hover">
                    @if ($homeView == true)
                        <thead>
                            <th scope="col">No.</th>
                            <th scope="col">Kos</th>
                            <th scope="col">Admin</th>
                            <th scope="col">Kamar</th>
                            <th scope="col">Lihat</th>
                        </thead>
                        <tbody>
                            @if ($manyRoom)
                            @foreach ($manyRoom as $item => $value)
                                <tr>
                                    <td>{{$item+1}}</td>
                                    <td>{{$value['kos']}}</td>
                                    <td>{{$value['admin']}}</td>
                                    <td>{{$value['name']}}</td>
                                    <td>
                                        <button wire:click="roomDetail({{$value['id']}}, false)" type="button" class="btn btn-primary">Lihat</button>
                                    </td>
                                </tr>
                            @endforeach
                            @else
                            <tr>
                                Data masih kosong
                            </tr>
                            @endif
                        </tbody>
                    @else
                        <div class="row">
                            <label class="col-sm-1">Kos</label>
                            <div class="col">
                                : {{$kosName}}
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-1">Kamar</label>
                            <div class="col">
                                : {{$roomName}}
                            </div>
                        </div>
                        <thead>
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Waktu</th>
                                <th scope="col">Tarif</th>
                                <th scope="col">KWh</th>
                                <th scope="col">Tagihan</th>
                                @if ($mode == 1)
                                    <th scope="col">Status</th>
                                @endif
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($mode == 0)
                                @for ($i=0; $i<$totalYear; $i++)
                                    <tr>
                                        <td class="text-left">
                                            {{$i+1}}
                                        </td>
                                        <td class="text-left">
                                            {{$manyYear[$i]}}
                                        </td>
                                        <td class="text-left">
                                            Rp
                                            {{number_format($roomTariff[$i], 2, ',', '.')}}
                                        </td>
                                        <td class="text-left">
                                            {{-- @if (strlen($yearlyKwh[$i]) > 5)
                                                {{number_format($yearlyKwh[$i], 6, ',', '.')}}
                                            @else --}}
                                                {{floatval($yearlyKwh[$i])}}
                                            {{-- @endif --}}
                                            Kwh
                                        </td>
                                        <td class="text-left">
                                            Rp
                                            {{number_format($yearlyBill[$i], 2, ',', '.')}}
                                        </td>
                                        <td class="text-left">
                                            <button wire:click="selectMode(1, {{$manyYear[$i]}})" type="button" class="btn btn-primary">Lihat</button>
                                        </td>
                                    </tr>
                                @endfor
                            @else
                                @foreach($selectedDate as $data => $value)
                                    <tr>
                                        <td class="text-left">
                                            {{$data+1}}
                                        </td>
                                        <td class="text-left">
                                            {{$value}}
                                        </td>
                                        <td class="text-left">
                                            Rp
                                            {{number_format($selectedTariff[$data], 2, ',', '.')}}
                                        </td>
                                        <td class="text-left">
                                            @if (strlen($selectedKwh[$data]) > 5)
                                                {{number_format($selectedKwh[$data], 6, ',', '.')}}
                                            @else
                                                {{floatval($selectedKwh[$data])}}
                                            @endif
                                            Kwh
                                        </td>
                                        <td class="text-left">
                                            Rp
                                            {{number_format($selectedBill[$data], 2, ',', '.')}}
                                        </td>
                                        @if ($mode == 1)
                                            <td>
                                                @if ($selectedStatus[$data] === 0)
                                                    <button class="btn btn-sm btn-outline-danger" disabled>Belum bayar</button>
                                                @elseif($selectedStatus[$data] === null)
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>Bulan ini</button>
                                                @else
                                                    <button class="btn btn-sm btn-outline-success" disabled>Sudah bayar</button>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="text-left">
                                            @if ($mode == 1)
                                                <button wire:click="selectMode(2, {{(int)$value}})" type="button" class="btn btn-primary">Lihat</button>
                                            @elseif ($mode == 2)
                                                <button wire:click="selectMode(3, {{(int)$value}})" type="button" class="btn btn-primary">Lihat</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    @endif
                </table>
                @if ($mode != 0)
                    <button wire:click="selectMode(0, 0)" type="button" class="btn btn-secondary">Kembali ke Tahun</button>
                @endif
                @if ($homeView == false)
                    <button wire:click="roomDetail(0, true)" type="button" class="btn btn-secondary">Kembali ke Kos</button>
                @endif
            </form>
        </div>
    </div>
</div>
