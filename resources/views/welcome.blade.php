<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ZaroLead</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/demo_2/style.css') }}">
        <style>
            html, body {
                background-color: #0c1427;
                color: #bfc3ce;
                font-family: 'Overpass', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
                font-weight: 900;
                color: #bfc3ce;
            }

            .title-right-span {
                font-weight: 300;
                color: #727cf5;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Zaro<span class="title-right-span">Lead</span>
                </div>

                <div class="links">
                    <a href="https://laravel.com/docs"><i class="link-icon" data-feather="info"></i> Docs</a>
                    <a href="{{ route('dashboard') }}"><i class="link-icon" data-feather="box"></i> Dashboard</a>
                    <a href="#"><i class="link-icon" data-feather="box"></i> News</a>
                    <a href="#"><i class="link-icon" data-feather="box"></i> Forge</a>
                    <a href="https://github.com/driverfury/zarolead"><i class="link-icon" data-feather="github"></i> GitHub</a>
                </div>
            </div>
        </div>

        <!-- JS scripts -->
        <script src="{{ asset('assets/vendors/core/core.js') }}"></script>
        <script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('assets/js/template.js') }}"></script>

    </body>
</html>
