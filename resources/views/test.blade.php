<html>

<head>
    <!-- VueJS CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Fonts -->
	<link rel="stylesheet" href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}">

    <!-- Vendors CSS -->
	<link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}">

    <!-- Layout CSS -->
	<link rel="stylesheet" href="{{ asset('assets/css/demo_2/style.css') }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
</head>

<body>
    <div id="app">
        <router-view></router-view>
    </div>

    <!-- Vue Entry Point -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Template JS -->
    <!--
    <script src="{{ asset('vendor/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-ui.min.js') }}"></script>
    -->
    <script src="{{ asset('assets/js/template.js') }}"></script>

    <!-- Plugins JS -->
    <script src="{{ asset('assets/vendors/core/core.js') }}"></script>
    <script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery.flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery.flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!--<script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>-->
    <script src="{{ asset('assets/vendors/progressbar.js/progressbar.min.js') }}"></script>

    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
</body>

</html>
