<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Smart Kos Energy Management with Internet of Things">
        <meta name="author" content="Ied Fajar Heryan">

        <title>IoT Smart Kos - Kos Management</title>

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
                {{-- <hr class="sidebar-divider my-0"> --}}

                {{-- Nav Item - Dashboard --}}
                {{-- <li class="nav-item">
                    <a class="nav-link" href="{{ url('/dashboard/admin') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span></a>
                </li> --}}

                {{-- Divider --}}
                <hr class="sidebar-divider">

                {{-- Heading --}}
                <div class="sidebar-heading">
                    Profile
                </div>

                {{-- Nav Item - Pages Collapse Menu --}}
                <li class="nav-item active">
                    <a class="nav-link" href="{{ url('/dashboard/admin/profile') }}">
                        <i>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                            </svg>
                        </i>
                        <span>Admin Profile</span>
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
                    <a class="nav-link" href="{{ url('/dashboard/admin/kos') }}">
                        <i class="bi bi-building-fill">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building-fill" viewBox="0 0 16 16">
                                <path d="M3 0a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h3v-3.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V16h3a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm1 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5M4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5"/>
                            </svg>
                        </i>
                        <span>Kos Saya</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/dashboard/admin/tagihan') }}">
                        <i class="bi bi-lightning-fill">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lightning-fill" viewBox="0 0 16 16">
                                <path d="M5.52.359A.5.5 0 0 1 6 0h4a.5.5 0 0 1 .474.658L8.694 6H12.5a.5.5 0 0 1 .395.807l-7 9a.5.5 0 0 1-.873-.454L6.823 9.5H3.5a.5.5 0 0 1-.48-.641z"/>
                            </svg>
                        </i>
                        <span>Tagihan Listrik</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/dashboard/admin/request') }}">
                        <i class="bi bi-person-exclamation">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-exclamation" viewBox="0 0 16 16">
                                <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m.256 7a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/>
                                <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-3.5-2a.5.5 0 0 0-.5.5v1.5a.5.5 0 0 0 1 0V11a.5.5 0 0 0-.5-.5m0 4a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                            </svg>
                        </i>
                        <span>Permintaan Penghunian</span>
                    </a>
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
            @livewire('AdminProfile')
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
