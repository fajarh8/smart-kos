<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Smart Kos Energy Management with Internet of Things">
        <meta name="author" content="Ied Fajar Heryan">

        <title>IoT Smart Kos - Profil</title>

        <style>
            /* The switch - the box around the slider */
            .switch {
            /* margin: 5px 0px -12px 0px; */
            position: relative;
            display: inline-block;
            width: 30px;
            height: 17px;
            scale: 1;
            }

            /* Hide default HTML checkbox */
            .switch input {
            opacity: 0;
            width: 0;
            height: 0;
            }

            /* The slider */
            .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
            }

            .slider:before {
            position: absolute;
            content: "";
            height: 13px;
            width: 13px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
            }

            input:checked + .slider {
            background-color: #2196F3;
            }

            input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
            }

            input:checked + .slider:before {
            -webkit-transform: translateX(13px);
            -ms-transform: translateX(13px);
            transform: translateX(13px);
            }

            /* Rounded sliders */
            .slider.round {
            border-radius: 17px;
            }

            .slider.round:before {
            border-radius: 50%;
            }
        </style>

        @vite([
            'resources/css/app.css',
            'resources/js/app.js',
        ])

        {{-- Custom fonts for this template --}}
        <link href="{{ URL::asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet"
        >

        {{-- Custom styles for this template --}}
        <link href="{{ URL::asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    </head>
    <body id="page-top">

        {{-- Page Wrapper --}}
        <div id="wrapper">

            {{-- Sidebar --}}
            <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

                {{-- Sidebar - Brand --}}
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
                    <div class="sidebar-brand-icon rotate-n-15">
                        <i class="fas fa-laugh-wink"></i>
                    </div>
                    <div class="sidebar-brand-text mx-3 text-left">Smart Kos IoT</div>
                </a>

                {{-- Divider --}}
                <hr class="sidebar-divider my-0">

                {{-- Nav Item - Dashboard --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/dashboard/user') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span></a>
                </li>

                {{-- Divider --}}
                <hr class="sidebar-divider">

                {{-- Heading --}}
                <div class="sidebar-heading">
                    Profile
                </div>

                {{-- Nav Item - Pages Collapse Menu --}}

                <li class="nav-item active">
                    <a class="nav-link" href="{{ url('/dashboard/user/userprofile') }}">
                        {{-- <i class="fas fa-fw fa-cog"></i> --}}
                        {{-- <i class="bi bi-person-fill"></i> --}}
                        <i>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                            </svg>
                        </i>
                        <span>User Profile</span>
                    </a>
                </li>

                {{-- Divider --}}
                <hr class="sidebar-divider">

                {{-- Heading --}}
                <div class="sidebar-heading">
                    Menu
                </div>

                {{-- Nav Item - Pages Collapse Menu --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/dashboard/user/automation') }}">
                        {{-- <i class="fas fa-fw fa-chart-area"></i> --}}
                        <i class="bi bi-lightbulb-fill">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lightbulb-fill" viewBox="0 0 16 16">
                                <path d="M2 6a6 6 0 1 1 10.174 4.31c-.203.196-.359.4-.453.619l-.762 1.769A.5.5 0 0 1 10.5 13h-5a.5.5 0 0 1-.46-.302l-.761-1.77a2 2 0 0 0-.453-.618A5.98 5.98 0 0 1 2 6m3 8.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1l-.224.447a1 1 0 0 1-.894.553H6.618a1 1 0 0 1-.894-.553L5.5 15a.5.5 0 0 1-.5-.5"/>
                            </svg>
                        </i>
                        <span>Automasi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/dashboard/user/history') }}">
                        {{-- <i class="fas fa-fw fa-chart-area"></i> --}}
                        <i class="bi bi-clock-history">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                                <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                                <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                                <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                            </svg>
                        </i>
                        <span>Riwayat</span>
                    </a>
                </li>
                {{-- Nav Item - Charts --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/dashboard/user/kossearch') }}">
                        {{-- <i class="fas fa-fw fa-chart-area"></i> --}}
                        <i class="bi bi-search">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                        </i>
                        <span>Cari Kos</span></a>
                </li>

                {{-- Divider --}}
                <hr class="sidebar-divider d-none d-md-block">

                {{-- {{-- Sidebar Toggler (Sidebar) --}}
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>

            </ul>
            {{-- End of Sidebar --}}

            {{-- Content Wrapper --}}
            <div id="content-wrapper" class="d-flex flex-column">

                {{-- Main Content --}}
                <div id="content">

                    {{-- Topbar --}}
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                        {{-- Topbar Search --}}
                        <div class="nav-link text-left font-weight-normal">
                            {{$kosName}} | {{$roomName}}
                        </div>
                        <div class="topbar-divider d-none d-sm-block"></div>


                        {{-- Topbar Navbar --}}
                        <ul class="navbar-nav ml-auto">
                            <div class="topbar-divider d-none d-sm-block"></div>
                            {{-- Nav Item - User Information --}}
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{Auth::user()->name}}</span>
                                    <img class="img-profile rounded-circle"
                                        src="{{ URL::asset('img/undraw_profile.svg') }}">
                                </a>
                                {{-- Dropdown - User Information --}}
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="{{ url('/dashboard/user/userprofile') }}">
                                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Profile
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </a>
                                </div>
                            </li>

                        </ul>

                    </nav>
                    {{-- End of Topbar --}}

            {{-- Content Wrapper --}}
            @livewire('UserProfile')
            @yield('content')

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Ied Fajar Heryan 2024</span>
                    </div>
                </div>
            </footer>
            </div>
            {{-- End of Main Content --}}
            {{-- Footer --}}
            {{-- End of Footer --}}
            {{-- End of Content Wrapper --}}

        </div>
        {{-- End of Page Wrapper --}}

        {{-- Scroll to Top Button --}}
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        {{-- Logout Modal --}}
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Logout</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Anda yakin ingin LOGOUT?</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <form action="GET">
                            @csrf
                            <a class="btn btn-primary" href="/logout">LOGOUT</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bootstrap core JavaScript --}}
        <script src="{{ URL::asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ URL::asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        {{-- Core plugin JavaScript --}}
        <script src="{{ URL::asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

        {{-- Custom scripts for all pages --}}
        <script src="{{ URL::asset('js/sb-admin-2.min.js') }}"></script>

    </body>
</html>
