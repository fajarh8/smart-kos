<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Smart Kos Energy Management with Internet of Things">
        <meta name="author" content="Ied Fajar Heryan">

        <title>IoT Smart Kos - Dashboard</title>

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
    <body>
        @include('user.user')
    </body>
</html>
