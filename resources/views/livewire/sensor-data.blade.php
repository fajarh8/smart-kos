{{-- Begin Page Content --}}
<div class="container-fluid">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}
    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        {{-- <a href="{{ url('/') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
            class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> --}}
    </div>
        {{-- @if ($energy) --}}
    @if (session()->has('danger'))
        <div class="">
            <div class="alert alert-danger">
                {{session('danger')}}
            </div>
        </div>
    @endif
    {{-- Content Row --}}
    <div class="row">
        {{-- Earnings (Monthly) Card Example --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Konsumsi Listrik (Bulan Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{number_format((float)$energy, 6, ',', '')}}
                                kWh
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Earnings (Monthly) Card Example --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tagihan Listrik (Bulan Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp
                                {{number_format($roomBill, 2, ',', '.')}}
                                {{-- {{$roomBill}} --}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Earnings (Monthly) Card Example --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Penggunaan Listrik Saat Ini
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{number_format((float)$apparentPower, 2, ',', '')}} / {{$maxPower}} VA</div>
                                </div>
                                {{-- <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: {{($apparentPower/$maxPower)*100}}%" aria-valuenow="50" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Requests Card Example --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Akses Listrik</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$electricAccess ? "Terhubung" : "Terputus"}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Row --}}
    <div class="row">

        {{-- Earnings (Monthly) Card Example --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Suhu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="temperature">
                                {{$temperature}}
                                °C
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Earnings (Monthly) Card Example --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Kelembapan</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{$humidity}}
                                        %
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: {{$humidity}}%" aria-valuenow="50" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Earnings (Monthly) Card Example --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Intensitas Cahaya</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{$ldr}} %
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: {{$ldr}}%" aria-valuenow="50" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Requests Card Example --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Keberadaan Orang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{$pir ? "Ada" : "Kosong"}}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Row --}}
    <div class="row">

        {{-- Area Chart --}}
        <div class="col-xl col-lg">
            <div class="card shadow mb-4">
                {{-- Card Header - Dropdown --}}
                <div
                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Konsumsi Listrik (Hari Ini)</h6>
                    <div class="dropdown no-arrow">
                        <a href="user/history" role="button">
                            {{-- <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i> --}}
                            Riwayat
                        </a>
                        {{-- <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="electricDropdownMenu">
                            <div class="dropdown-header">Lihat:</div>
                            <a class="dropdown-item" href="#">Riwayat Konsumsi Listrik</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div> --}}
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="card-body">
                    <div wire:ignore class="chart-area">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Perangkat Elektronik</h6>
                    <div class="dropdown no-arrow">
                        <a href="user/automation" role="button">Edit Automasi</a>
                    </div>
                </div>
                <div class="card-body pt-2 pb-3">
                    <table>
                        <thead>
                            <tr>
                                <th class="col- p-0 pr-2 text-left">No.</th>
                                <th class="col-xl text-left p-0">Perangkat</th>
                                <th class="col-sm text-left p-1">Power</th>
                                <th class="col-sm text-center p-2">Automasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($deviceId)
                                @for ($i = 0; $i < 6; $i++)
                                    <tr>
                                        <td class="text-left">{{$i+1}}</td>
                                        <td class="text-left">{{$relayLabel[$i]}}</td>
                                        <td class="text-center">
                                            @if ($relayPir[$i] == 1 && $pir == 0)
                                                <label class="switch mt-2 justify-content-center" wire:click="powerSwitch({{$i}}, 0)">
                                                    <input type="checkbox" {{$relayStatus[$i] ? "checked" : ""}} disabled>
                                                    <span class="slider round"></span>
                                                </label>
                                            @else
                                                <label class="switch mt-2 justify-content-center">
                                                    <input type="checkbox" {{$relayStatus[$i] ? "checked" : ""}} wire:click="powerSwitch({{$i}}, 1)">
                                                    <span class="slider round"></span>
                                                </label>
                                            @endif
                                        </td>
                                            <td class="text-center">
                                                <label class="switch mt-2">
                                                    <input wire:click="automationSwitch({{$i}})" type="checkbox" {{$relayAuto[$i] ? "checked" : ""}}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                    </tr>
                                @endfor
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if (session()->has('alert'))
                <div wire:ignore>
                    <div wire:ignore class="alert alert-danger">
                        {{session('alert')}}
                    </div>
                </div>
            @endif
        </div>
    {{-- @endif --}}

    </div>

    {{-- Content Row --}}
    {{-- <div class="row"> --}}

        {{-- Content Column --}}
        {{-- <div class="col-lg-6 mb-4"> --}}

            {{-- Project Card Example --}}
            {{-- <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monitor Listrik</h6>
                </div>
                <div class="card-body">
                    <h4 class="small font-weight-bold">Tegangan Listrik
                        <span
                            class="float-right">
                            {{$voltage}}
                            V
                        </span>
                    </h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-danger" role="progressbar" style="width:
                        {{(($voltage)/250)*100}}%"
                            aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <h4 class="small font-weight-bold">Arus Listrik <span
                            class="float-right">{{$current}} A</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-warning" role="progressbar"
                        style= "width:
                            {{
                                (($voltage)*($current))
                                /
                                (($voltage)*($maxPower/($voltage)))
                                *100
                            }}%"
                            aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <h4 class="small font-weight-bold">Daya Aktif <span
                            class="float-right">
                            {{$activePower}}
                            W</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar" role="progressbar" style="width: 0%"
                            aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <h4 class="small font-weight-bold">Daya Reaktif <span
                            class="float-right">
                            {{number_format((float)$reactivePower, 2, '.', '')}} / {{$maxPower}}
                             VA</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-info" role="progressbar" style="width:
                        {{($reactivePower/$maxPower)*100}}%"
                            aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <h4 class="small font-weight-bold">Power Factor <span
                            class="float-right">
                            {{$powerFactor*100}} / 100 %</span></h4>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width:
                        {{($powerFactor)*100}}%"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div> --}}

            {{-- Color System --}}
            {{-- <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            Primary
                            <div class="text-white-50 small">#4e73df</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-success text-white shadow">
                        <div class="card-body">
                            Success
                            <div class="text-white-50 small">#1cc88a</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-info text-white shadow">
                        <div class="card-body">
                            Info
                            <div class="text-white-50 small">#36b9cc</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-warning text-white shadow">
                        <div class="card-body">
                            Warning
                            <div class="text-white-50 small">#f6c23e</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-danger text-white shadow">
                        <div class="card-body">
                            Danger
                            <div class="text-white-50 small">#e74a3b</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-secondary text-white shadow">
                        <div class="card-body">
                            Secondary
                            <div class="text-white-50 small">#858796</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-light text-black shadow">
                        <div class="card-body">
                            Light
                            <div class="text-black-50 small">#f8f9fc</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card bg-dark text-white shadow">
                        <div class="card-body">
                            Dark
                            <div class="text-white-50 small">#5a5c69</div>
                        </div>
                    </div>
                </div>
            </div> --}}
        {{-- </div> --}}

        {{-- <div class="col-lg-6 mb-4"> --}}
            {{-- Project Card Example --}}
            {{-- <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Projects</h6>
                </div>
                <div class="card-body">
                    <h4 class="small font-weight-bold">Tegangan Listrik <span
                            class="float-right">20%</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 20%"
                            aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <h4 class="small font-weight-bold">Arus Listrik <span
                            class="float-right">40%</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 40%"
                            aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <h4 class="small font-weight-bold">Daya Aktif <span
                            class="float-right">60%</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar" role="progressbar" style="width: 60%"
                            aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <h4 class="small font-weight-bold">Daya Reaktif <span
                            class="float-right">80%</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 80%"
                            aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <h4 class="small font-weight-bold">Power Factor <span
                            class="float-right">Complete!</span></h4>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div> --}}

            {{-- Approach --}}
            {{-- <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Development Approach</h6>
                </div>
                <div class="card-body">
                    <p>SB Admin 2 makes extensive use of Bootstrap 4 utility classes in order to reduce
                        CSS bloat and poor page performance. Custom CSS classes are used to create
                        custom components and custom utility classes.</p>
                    <p class="mb-0">Before working with this theme, you should become familiar with the
                        Bootstrap framework, especially the utility classes.</p>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> --}}
    {{-- @push('js') --}}

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endassets

    @script
        <script>
            let ctx = document.getElementById("myChart");
            let datas = $wire.chartData;
            let labels = datas.map(item=>item.hour);
            let values = datas.map(item=>item.kwh);
            let myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "KWh",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: values,
                        xAxisID: 'xAxis',
                        yAxisID: 'yAxis',
                    }],
                },
                options: {
                    interaction: {
                        mode: 'x',
                        intersect: false,
                    },
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 0,
                            right: 0,
                            top: 0,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxis: {
                            // type: 'time',
                            grid: {
                                // display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        },
                        yAxis: {
                            ticks: {
                                maxTicksLimit: 6,
                                padding: 10,
                                // Include a dollar sign in the ticks
                                callback: function(value, index, ticks) {
                                    return parseFloat(value.toFixed(6));
                                }
                            },
                            grid: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                tickBorderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: "rgba(255,255,255,1)",
                            bodyColor: '#858796',
                            titleMarginBottom: 10,
                            titleColor: '#6e707e',
                            // titleFont:{
                            //     size: 14,
                            // }
                            borderColor: 'rgba(221,223,235,1)',
                            borderWidth: 1,
                            padding: {
                                x: 15,
                                y: 15,
                            },
                            displayColors: false,
                            intersect: false,
                            mode: 'index',
                            caretPadding: 10,
                            callbacks: {
                                label: function(context) {
                                    let datasetLabel = context.dataset.label || '';
                                    return context.parsed.y + ' ' + datasetLabel;
                                }
                            }
                        },
                    },
                }
            });
        </script>
    @endscript

</div>
