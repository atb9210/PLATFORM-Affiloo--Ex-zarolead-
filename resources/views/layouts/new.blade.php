<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ZaroLead</title>
	<!-- core:css -->
	<link rel="stylesheet" href="../assets/vendors/core/core.css">
	<!-- endinject -->
  <!-- plugin css for this page -->
	<link rel="stylesheet" href="../assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
	<!-- end plugin css for this page -->
	<!-- inject:css -->
	<link rel="stylesheet" href="../assets/fonts/feather-font/css/iconfont.css">
	<link rel="stylesheet" href="../assets/vendors/flag-icon-css/css/flag-icon.min.css">
	<!-- endinject -->
  <!-- Layout styles -->  
	<link rel="stylesheet" href="../assets/css/demo_2/style.css">
  <!-- End layout styles -->
  <link rel="shortcut icon" href="../assets/images/favicon.png" />
</head>
<body>
	<div class="main-wrapper">

		<!-- partial:partials/_sidebar.html -->
        @include('layouts/partials/_sidebar')
		<!-- partial -->
	
		<div class="page-wrapper">
					
			<!-- partial:partials/_navbar.html -->
			@include('layouts/partials/_navbar')
			<!-- partial -->

            <div class="page-content">
            @yield('content')
			</div>

			<!-- partial:partials/_footer.html -->
			@include('layouts/partials/_footer')
			<!-- partial -->
		
		</div>
	</div>

	<!-- core:js -->
	<script src="../assets/vendors/core/core.js"></script>
	<!-- endinject -->
  <!-- plugin js for this page -->
  <script src="../assets/vendors/chartjs/Chart.min.js"></script>
  <script src="../assets/vendors/jquery.flot/jquery.flot.js"></script>
  <script src="../assets/vendors/jquery.flot/jquery.flot.resize.js"></script>
  <script src="../assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
  <script src="../assets/vendors/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendors/progressbar.js/progressbar.min.js"></script>
	<!-- end plugin js for this page -->
	<!-- inject:js -->
	<script src="../assets/vendors/feather-icons/feather.min.js"></script>
	<script src="../assets/js/template.js"></script>
	<!-- endinject -->
  <!-- custom js for this page -->
  <script src="../assets/js/dashboard.js"></script>
  <script src="../assets/js/datepicker.js"></script>
	<!-- end custom js for this page -->
</body>
</html>
