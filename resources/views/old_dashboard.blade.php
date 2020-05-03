<!DOCTYPE html>
<html lang="it">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Dashboard</title>

    <!-- Fontfaces CSS-->
    <link href="{{ URL::asset('css/font-face.css') }}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('vendor/font-awesome-4.7/css/font-awesome.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('vendor/font-awesome-5/css/fontawesome-all.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('vendor/mdi-font/css/material-design-iconic-font.min.css') }}" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="{{ URL::asset('vendor/bootstrap-4.1/bootstrap.min.css') }}" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="{{ URL::asset('vendor/animsition/animsition.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('vendor/wow/animate.css') }}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('vendor/css-hamburgers/hamburgers.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('vendor/slick/slick.css') }}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('vendor/select2/select2.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('vendor/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="{{ URL::asset('css/theme.css') }}" rel="stylesheet" media="all">

	<!-- Custom CSS -->
	<style>
	.orders-filters, .product-filters {
        text-align: center;
        margin-right: auto;
        margin-left: auto;
        margin-bottom: 1.5em;
    }
	</style>
	
</head>

<body class="animsition">
    <div class="page-wrapper">
        <!-- HEADER MOBILE-->
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a class="logo" href="index.html">
                            <img src="{{ URL::asset('images/logo.png') }}" alt="ZaroLead" />
                        </a>
                        <button class="hamburger hamburger--slider" type="button">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <nav class="navbar-mobile">
                <div class="container-fluid">
                    <ul class="navbar-mobile__list list-unstyled">
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                            <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                                <li>
                                    <a href="index.html">Dashboard 1</a>
                                </li>
                                <li>
                                    <a href="index2.html">Dashboard 2</a>
                                </li>
                                <li>
                                    <a href="index3.html">Dashboard 3</a>
                                </li>
                                <li>
                                    <a href="index4.html">Dashboard 4</a>
                                </li>
                            </ul>
                        </li>
                        <!--
                        <li>
                            <a href="chart.html">
                                <i class="fas fa-chart-bar"></i>Charts</a>
                        </li>
                        -->
                        
                        @if (Auth::user()->hasPermission('admin') ||
                        Auth::user()->hasPermission('mediabuyer') ||
                        Auth::user()->hasPermission('affiliate') ||
                        Auth::user()->hasPermission('advertiser'))
                        <li>
                            <a href="{{ route('orders') }}">
                                <i class="fas fa-table"></i>Orders</a>
                        </li>
                        @endif

                        @if (Auth::user()->hasPermission('admin'))
                        <li>
                            <a href="{{ route('admin/users') }}">
                                <i class="fas fa-chart-line"></i>Manage Users</a>
                        </li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-chart-line"></i>Reports</a>
                            <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                                <li>
                                    <a href="{{ route('admin/report/users') }}">Report Users</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin/report/affiliates') }}">Report Affiliates</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin/report/products') }}">Report Products</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin/report/advertisers') }}">Report Advertisers</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin/report/mediabuyers') }}">Report Mediabuyers</a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if (Auth::user()->hasPermission('admin'))
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-th-large"></i>Assets</a>
                            <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                                <li>
                                    <a href="{{ route('advertisers') }}">Advertisers</a>
                                </li>
                                <li>
                                    <a href="{{ route('tags') }}">Tags</a>
                                </li>
                                <li>
                                    <a href="{{ route('products') }}">Products</a>
                                </li>
                                <li>
                                    <a href="{{ route('traffic-sources') }}">Traffic Sources</a>
                                </li>
                                <li>
                                    <a href="{{ route('landings') }}">Landings</a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if (Auth::user()->hasPermission('admin') || Auth::user()->hasPermission('mediabuyer'))
                        <li>
                            <a href="{{ route('form-builder') }}">
                                <i class="fas fa-plus"></i>Form Builder</a>
                        </li>
                        @endif

                        @if (Auth::user()->hasPermission('admin') || Auth::user()->hasPermission('mediabuyer') || Auth::user()->hasPermission('affiliate'))
                        <li>
                            <a href="{{ route('admin/forms') }}">
                                <i class="fas fa-chart-bar"></i>Report Forms</a>
                        </li>
                        @endif

                        <li>
                            <a href="{{ route('invoice/upload') }}">
                                <i class="fas fa-plus"></i>Upload Invoices</a>
                        </li>
                        @if (Auth::user()->hasPermission('admin') ||
                            Auth::user()->hasPermission('accountant'))
                            <li>
                                <a href="{{ route('admin/invoices') }}">
                                    <i class="fas fa-file-pdf"></i>Invoices</a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('invoices') }}">
                                    <i class="fas fa-file-pdf"></i>Invoices</a>
                            </li>
                        @endif

                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-power-off"></i>Logout
                            </a>
                        </li>

                        <!--
                        <li>
                            <a href="form.html">
                                <i class="far fa-check-square"></i>Forms</a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-calendar-alt"></i>Calendar</a>
                        </li>
                        <li>
                            <a href="map.html">
                                <i class="fas fa-map-marker-alt"></i>Maps</a>
                        </li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-copy"></i>Pages</a>
                            <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                                <li>
                                    <a href="login.html">Login</a>
                                </li>
                                <li>
                                    <a href="register.html">Register</a>
                                </li>
                                <li>
                                    <a href="forget-pass.html">Forget Password</a>
                                </li>
                            </ul>
                        </li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-desktop"></i>UI Elements</a>
                            <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                                <li>
                                    <a href="button.html">Button</a>
                                </li>
                                <li>
                                    <a href="badge.html">Badges</a>
                                </li>
                                <li>
                                    <a href="tab.html">Tabs</a>
                                </li>
                                <li>
                                    <a href="card.html">Cards</a>
                                </li>
                                <li>
                                    <a href="alert.html">Alerts</a>
                                </li>
                                <li>
                                    <a href="progress-bar.html">Progress Bars</a>
                                </li>
                                <li>
                                    <a href="modal.html">Modals</a>
                                </li>
                                <li>
                                    <a href="switch.html">Switchs</a>
                                </li>
                                <li>
                                    <a href="grid.html">Grids</a>
                                </li>
                                <li>
                                    <a href="fontawesome.html">Fontawesome Icon</a>
                                </li>
                                <li>
                                    <a href="typo.html">Typography</a>
                                </li>
                            </ul>
                        </li>
                        -->
                    </ul>
                </div>
            </nav>
        </header>
        <!-- END HEADER MOBILE-->

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="#">
                    <img src="{{ URL::asset('images/logo.png') }}" alt="ZaroLead" />
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li class="active has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                            <!--
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="index.html">Dashboard 1</a>
                                </li>
                                <li>
                                    <a href="index2.html">Dashboard 2</a>
                                </li>
                                <li>
                                    <a href="index3.html">Dashboard 3</a>
                                </li>
                                <li>
                                    <a href="index4.html">Dashboard 4</a>
                                </li>
                            </ul>
                            -->
                        </li>
                        <!--
                        <li>
                            <a href="chart.html">
                                <i class="fas fa-chart-bar"></i>Charts</a>
                        </li>
                        -->

                        @if (Auth::user()->hasPermission('admin') ||
                        Auth::user()->hasPermission('mediabuyer') ||
                        Auth::user()->hasPermission('affiliate') ||
                        Auth::user()->hasPermission('advertiser'))
                        <li>
                            <a href="{{ route('orders') }}">
                                <i class="fas fa-table"></i>Orders</a>
                        </li>
                        @endif

                        @if (Auth::user()->hasPermission('admin'))
                        <li>
                            <a href="{{ route('admin/users') }}">
                                <i class="fas fa-users"></i>Manage Users</a>
                        </li>

                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-chart-line"></i>Reports</a>
                            <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                                <li>
                                    <a href="{{ route('admin/report/users') }}">Report Users</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin/report/affiliates') }}">Report Affiliates</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin/report/products') }}">Report Products</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin/report/advertisers') }}">Report Advertisers</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin/report/mediabuyers') }}">Report Mediabuyers</a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if (Auth::user()->hasPermission('admin'))
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-th-large"></i>Assets</a>
                            <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                                <li>
                                    <a href="{{ route('advertisers') }}">Advertisers</a>
                                </li>
                                <li>
                                    <a href="{{ route('tags') }}">Tags</a>
                                </li>
                                <li>
                                    <a href="{{ route('products') }}">Products</a>
                                </li>
                                <li>
                                    <a href="{{ route('traffic-sources') }}">Traffic Sources</a>
                                </li>
                                <li>
                                    <a href="{{ route('landings') }}">Landings</a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        
                        @if (Auth::user()->hasPermission('admin') || Auth::user()->hasPermission('mediabuyer'))
                        <li>
                            <a href="{{ route('form-builder') }}">
                                <i class="fas fa-plus"></i>Form Builder</a>
                        </li>
                        @endif

                        @if (Auth::user()->hasPermission('admin') || Auth::user()->hasPermission('mediabuyer') || Auth::user()->hasPermission('affiliate'))
                        <li>
                            <a href="{{ route('admin/forms') }}">
                                <i class="fas fa-chart-bar"></i>Report Forms</a>
                        </li>
                        @endif

                        <li>
                            <a href="{{ route('invoice/upload') }}">
                                <i class="fas fa-plus"></i>Upload Invoices</a>
                        </li>
                        @if (Auth::user()->hasPermission('admin'))
                            <li>
                                <a href="{{ route('admin/invoices') }}">
                                    <i class="fas fa-file-pdf"></i>Invoices</a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('invoices') }}">
                                    <i class="fas fa-file-pdf"></i>Invoices</a>
                            </li>
                        @endif

                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-power-off"></i>Logout
                            </a>
                        </li>
                        
                        <!--
                        <li>
                            <a href="form.html">
                                <i class="far fa-check-square"></i>Forms</a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-calendar-alt"></i>Calendar</a>
                        </li>
                        <li>
                            <a href="map.html">
                                <i class="fas fa-map-marker-alt"></i>Maps</a>
                        </li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-copy"></i>Pages</a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="login.html">Login</a>
                                </li>
                                <li>
                                    <a href="register.html">Register</a>
                                </li>
                                <li>
                                    <a href="forget-pass.html">Forget Password</a>
                                </li>
                            </ul>
                        </li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-desktop"></i>UI Elements</a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="button.html">Button</a>
                                </li>
                                <li>
                                    <a href="badge.html">Badges</a>
                                </li>
                                <li>
                                    <a href="tab.html">Tabs</a>
                                </li>
                                <li>
                                    <a href="card.html">Cards</a>
                                </li>
                                <li>
                                    <a href="alert.html">Alerts</a>
                                </li>
                                <li>
                                    <a href="progress-bar.html">Progress Bars</a>
                                </li>
                                <li>
                                    <a href="modal.html">Modals</a>
                                </li>
                                <li>
                                    <a href="switch.html">Switchs</a>
                                </li>
                                <li>
                                    <a href="grid.html">Grids</a>
                                </li>
                                <li>
                                    <a href="fontawesome.html">Fontawesome Icon</a>
                                </li>
                                <li>
                                    <a href="typo.html">Typography</a>
                                </li>
                            </ul>
                        </li>
                        -->
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <!--
            <header class="header-desktop">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <form class="form-header" action="" method="POST">
                                <input class="au-input au-input--xl" type="text" name="search" placeholder="Search for datas &amp; reports..." />
                                <button class="au-btn--submit" type="submit">
                                    <i class="zmdi zmdi-search"></i>
                                </button>
                            </form>
                            <div class="header-button">
                                <div class="noti-wrap">
                                    <div class="noti__item js-item-menu">
                                        <i class="zmdi zmdi-comment-more"></i>
                                        <span class="quantity">1</span>
                                        <div class="mess-dropdown js-dropdown">
                                            <div class="mess__title">
                                                <p>You have 2 news message</p>
                                            </div>
                                            <div class="mess__item">
                                                <div class="image img-cir img-40">
                                                    <img src="images/icon/avatar-06.jpg" alt="Michelle Moreno" />
                                                </div>
                                                <div class="content">
                                                    <h6>Michelle Moreno</h6>
                                                    <p>Have sent a photo</p>
                                                    <span class="time">3 min ago</span>
                                                </div>
                                            </div>
                                            <div class="mess__item">
                                                <div class="image img-cir img-40">
                                                    <img src="images/icon/avatar-04.jpg" alt="Diane Myers" />
                                                </div>
                                                <div class="content">
                                                    <h6>Diane Myers</h6>
                                                    <p>You are now connected on message</p>
                                                    <span class="time">Yesterday</span>
                                                </div>
                                            </div>
                                            <div class="mess__footer">
                                                <a href="#">View all messages</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="noti__item js-item-menu">
                                        <i class="zmdi zmdi-email"></i>
                                        <span class="quantity">1</span>
                                        <div class="email-dropdown js-dropdown">
                                            <div class="email__title">
                                                <p>You have 3 New Emails</p>
                                            </div>
                                            <div class="email__item">
                                                <div class="image img-cir img-40">
                                                    <img src="images/icon/avatar-06.jpg" alt="Cynthia Harvey" />
                                                </div>
                                                <div class="content">
                                                    <p>Meeting about new dashboard...</p>
                                                    <span>Cynthia Harvey, 3 min ago</span>
                                                </div>
                                            </div>
                                            <div class="email__item">
                                                <div class="image img-cir img-40">
                                                    <img src="images/icon/avatar-05.jpg" alt="Cynthia Harvey" />
                                                </div>
                                                <div class="content">
                                                    <p>Meeting about new dashboard...</p>
                                                    <span>Cynthia Harvey, Yesterday</span>
                                                </div>
                                            </div>
                                            <div class="email__item">
                                                <div class="image img-cir img-40">
                                                    <img src="images/icon/avatar-04.jpg" alt="Cynthia Harvey" />
                                                </div>
                                                <div class="content">
                                                    <p>Meeting about new dashboard...</p>
                                                    <span>Cynthia Harvey, April 12,,2018</span>
                                                </div>
                                            </div>
                                            <div class="email__footer">
                                                <a href="#">See all emails</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="noti__item js-item-menu">
                                        <i class="zmdi zmdi-notifications"></i>
                                        <span class="quantity">3</span>
                                        <div class="notifi-dropdown js-dropdown">
                                            <div class="notifi__title">
                                                <p>You have 3 Notifications</p>
                                            </div>
                                            <div class="notifi__item">
                                                <div class="bg-c1 img-cir img-40">
                                                    <i class="zmdi zmdi-email-open"></i>
                                                </div>
                                                <div class="content">
                                                    <p>You got a email notification</p>
                                                    <span class="date">April 12, 2018 06:50</span>
                                                </div>
                                            </div>
                                            <div class="notifi__item">
                                                <div class="bg-c2 img-cir img-40">
                                                    <i class="zmdi zmdi-account-box"></i>
                                                </div>
                                                <div class="content">
                                                    <p>Your account has been blocked</p>
                                                    <span class="date">April 12, 2018 06:50</span>
                                                </div>
                                            </div>
                                            <div class="notifi__item">
                                                <div class="bg-c3 img-cir img-40">
                                                    <i class="zmdi zmdi-file-text"></i>
                                                </div>
                                                <div class="content">
                                                    <p>You got a new file</p>
                                                    <span class="date">April 12, 2018 06:50</span>
                                                </div>
                                            </div>
                                            <div class="notifi__footer">
                                                <a href="#">All notifications</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="account-wrap">
                                    <div class="account-item clearfix js-item-menu">
                                        <div class="image">
                                            <img src="images/icon/avatar-01.jpg" alt="John Doe" />
                                        </div>
                                        <div class="content">
                                            <a class="js-acc-btn" href="#">asraf</a>
                                        </div>
                                        <div class="account-dropdown js-dropdown">
                                            <div class="info clearfix">
                                                <div class="image">
                                                    <a href="#">
                                                        <img src="images/icon/avatar-01.jpg" alt="John Doe" />
                                                    </a>
                                                </div>
                                                <div class="content">
                                                    <h5 class="name">
                                                        <a href="#">asraf</a>
                                                    </h5>
                                                    <span class="email">asraf@asraf.com</span>
                                                </div>
                                            </div>
                                            <div class="account-dropdown__body">
                                                <div class="account-dropdown__item">
                                                    <a href="#">
                                                        <i class="zmdi zmdi-account"></i>Account</a>
                                                </div>
                                                <div class="account-dropdown__item">
                                                    <a href="#">
                                                        <i class="zmdi zmdi-settings"></i>Setting</a>
                                                </div>
                                                <div class="account-dropdown__item">
                                                    <a href="#">
                                                        <i class="zmdi zmdi-money-box"></i>Billing</a>
                                                </div>
                                            </div>
                                            <div class="account-dropdown__footer">
                                                <a href="#">
                                                    <i class="zmdi zmdi-power"></i>Logout</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            -->
            <!-- HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <?php
				if (!isset($period)) {
					$period = 'sempre';
				}
				$periodString = ' (' . $period . ')';
				
				setlocale(LC_MONETARY, 'it_IT');
                $fmt = new NumberFormatter('it_IT', NumberFormatter::CURRENCY);

                $orders_commission = $fmt->formatCurrency($orders_count['Tutti']['total'], "EUR");
                if (Auth::user()->hasPermission('admin') || Auth::user()->hasPermission('mediabuyer')) {
                    $orders_commission = $fmt->formatCurrency($orders_count['Tutti']['total'], "EUR");
                }
				
				if (!isset($filter_date_from)) {
					$filter_date_from = date('Y-m-d');
				}
				if (!isset($filter_date_to)) {
					$filter_date_to = date('Y-m-d');
				}
            ?>
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="overview-wrap">
                                    <h2 class="title-1">dashboard</h2>
                                    <!--
                                    <button class="au-btn au-btn-icon au-btn--blue">
                                        <i class="zmdi zmdi-plus"></i>add item</button>
                                    -->
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-25">
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c1">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-account-o"></i>
                                            </div>
                                            <div class="text">
                                                <h2>{{ $orders_count['network_sent']['count'] }}</h2>
                                                <span>Nuovi{{ $periodString }}</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <canvas id="widgetChart1"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c2">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-money"></i>
                                            </div>
                                            <div class="text">
                                                <?php
                                                    $attesa_conferma = 0.0;
                                                    /*
                                                    if (!Auth::user()->hasPermission('admin') && Auth::user()->hasPermission('affiliate')) {
                                                        $attesa_conferma = floatval($orders_count['Attesa']['sub_commission']) + floatval($orders_count['Conferma']['sub_commission']);
                                                    } else {
                                                        $attesa_conferma = floatval($orders_count['Attesa']['total']) + floatval($orders_count['Conferma']['total']);
                                                    }
                                                    */
                                                    $attesa_conferma = floatval($orders_count['network_sent']['total']);
                                                ?>
												<h2>{{ $attesa_conferma }} &euro;</h2>
                                                <span>Commissioni Attesa/Conferma{{ $periodString }}</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <canvas id="widgetChart1"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c4">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-shopping-cart"></i>
                                            </div>
                                            <div class="text">
                                                <h2>{{ $orders_count['Conferma']['count'] }}</h2>
                                                <span>Confermati{{ $periodString }}</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <canvas id="widgetChart2"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<!--
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c3">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-calendar-note"></i>
                                            </div>
                                            <div class="text">
                                                <h2>10</h2>
                                                <span>Spedizione</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <canvas id="widgetChart3"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c3">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="zmdi zmdi-money"></i>
                                            </div>
                                            <div class="text">
                                                <h2>3</h2>
                                                <span>Consegnati</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <canvas id="widgetChart4"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
							-->
                        </div>
						<!--
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="au-card recent-report">
                                    <div class="au-card-inner">
                                        <h3 class="title-2">recent reports</h3>
                                        <div class="chart-info">
                                            <div class="chart-info__left">
                                                <div class="chart-note">
                                                    <span class="dot dot--blue"></span>
                                                    <span>products</span>
                                                </div>
                                                <div class="chart-note mr-0">
                                                    <span class="dot dot--green"></span>
                                                    <span>services</span>
                                                </div>
                                            </div>
                                            <div class="chart-info__right">
                                                <div class="chart-statis">
                                                    <span class="index incre">
                                                        <i class="zmdi zmdi-long-arrow-up"></i>25%</span>
                                                    <span class="label">products</span>
                                                </div>
                                                <div class="chart-statis mr-0">
                                                    <span class="index decre">
                                                        <i class="zmdi zmdi-long-arrow-down"></i>10%</span>
                                                    <span class="label">services</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="recent-report__chart">
                                            <canvas id="recent-rep-chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						-->
                        <div class="row">
                            <div class="col-lg-12">
                                <h2 class="title-1 m-b-25">Lista ordini</h2>
								<div class="orders-filters">
                                    <div class="orders-filters-shortcuts">
    									<button class="btn btn-primary" onclick="dashboardDateShortcut('today');">Oggi</button>
    									<button class="btn btn-primary" onclick="dashboardDateShortcut('yesterday');">Ieri</button>
    									<button class="btn btn-primary" onclick="dashboardDateShortcut('this month');">Questo Mese</button>
    									<button class="btn btn-primary" onclick="dashboardDateShortcut('last month');">Mese Scorso</button>
                                    </div>
                                    <?php
                                        $formRouteName = 'dashboard';
                                        if ($admin) {
                                            $formRouteName = 'admin/' . $formRouteName;
                                        }
                                    ?>
                                    <form method="post" action="{{ route($formRouteName) }}" id="filter_data_form">
                                        @csrf
    									Da: <input type="date" value="{{ $filter_date_from }}" id="filter_date_from" name="filter_date_from">
    									A: <input type="date" value="{{ $filter_date_to }}" id="filter_date_to" name="filter_date_to">
                                        <input type="hidden" name="filter_product_name" value="{{ $filter_product_name }}">
    									<input type="submit" class="btn btn-primary" value="Filtra">
                                    </form>
								</div>
                                <div class="product-filters">
                                    <form method="post" action="{{ route($formRouteName) }}">
                                        @csrf
                                        <input type="hidden" name="filter_date_from" value="{{ $filter_date_from }}">
                                        <input type="hidden" name="filter_date_to" value="{{ $filter_date_to }}">
                                        <input type="text" name="filter_product_name" placeholder="Nome prodotto" value="{{ $filter_product_name }}">
                                        <input type="submit" class="btn btn-primary" value="Cerca prodotto">
                                    </form>
                                </div>
                                <div style="text-align: center;">
                                    Risultati totali: {{ $orders_count['Tutti']['count'] }} ({{ $page_size }} per pagina)
                                </div>
                                <div class="table-responsive table--no-card m-b-40">
                                    <table class="table table-borderless table-striped table-earning">
                                        <thead>
                                            <tr>
                                                <th>data</th>
                                                <th>ID</th>
                                                @if ($admin)
                                                    <th>Partner ID</th>
                                                @endif
                                                <th>prodotto</th>
												<th>commissione</th>

                                                @if(!Auth::user()->hasPermission('affiliate'))
												    <th>network</th>
                                                @endif

                                                <th class="text-right">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                            <?php
                                                $commission = 0.0;
                                                if (!Auth::user()->hasPermission('admin') && Auth::user()->hasPermission('affiliate')) {
                                                    $order_info = json_decode($order->info, TRUE);
                                                    if (isset($order_info['sub_commission'])) {
                                                        $commission = floatval($order_info['sub_commission']);
                                                    }
                                                } else {
                                                    $commission = $order->commission;
                                                }
                                            ?>
                                            <tr>
                                                <td>{{ $order->created_at }}</td>
                                                <td>{{ $order->id }}</td>
                                                @if ($admin)
                                                    <td>{{ $order->user_id }}</td>
                                                @endif
                                                <td>{{ $order->product }}</td>
                                                <td>&euro; {{ $commission }}</td>

                                                @if(!Auth::user()->hasPermission('affiliate'))
                                                    <td>{{ $order->network }}</td>
                                                @endif

                                                <td>{{ $order->status->description }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div style="margin-left: auto; margin-right: auto; width: auto; margin-bottom: 30px;">
                                    {{ $orders->links() }}
                                </div>
                            </div>
							<!--
                            <div class="col-lg-3">
                                <h2 class="title-1 m-b-25">Top countries</h2>
                                <div class="au-card au-card--bg-blue au-card-top-countries m-b-40">
                                    <div class="au-card-inner">
                                        <div class="table-responsive">
                                            <table class="table table-top-countries">
                                                <tbody>
                                                    <tr>
                                                        <td>United States</td>
                                                        <td class="text-right">$119,366.96</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Australia</td>
                                                        <td class="text-right">$70,261.65</td>
                                                    </tr>
                                                    <tr>
                                                        <td>United Kingdom</td>
                                                        <td class="text-right">$46,399.22</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Turkey</td>
                                                        <td class="text-right">$35,364.90</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Germany</td>
                                                        <td class="text-right">$20,366.96</td>
                                                    </tr>
                                                    <tr>
                                                        <td>France</td>
                                                        <td class="text-right">$10,366.96</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Australia</td>
                                                        <td class="text-right">$5,366.96</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Italy</td>
                                                        <td class="text-right">$1639.32</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
							-->
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
                                    <div class="au-card-title" style="background-image:url('images/bg-title-01.jpg');">
                                        <div class="bg-overlay bg-overlay--blue"></div>
                                        <h3>
                                            <i class="zmdi zmdi-account-calendar"></i>26 April, 2018</h3>
                                        <button class="au-btn-plus">
                                            <i class="zmdi zmdi-plus"></i>
                                        </button>
                                    </div>
                                    <div class="au-task js-list-load">
                                        <div class="au-task__title">
                                            <p>Tasks for John Doe</p>
                                        </div>
                                        <div class="au-task-list js-scrollbar3">
                                            <div class="au-task__item au-task__item--danger">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Meeting about plan for Admin Template 2018</a>
                                                    </h5>
                                                    <span class="time">10:00 AM</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--warning">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Create new task for Dashboard</a>
                                                    </h5>
                                                    <span class="time">11:00 AM</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--primary">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Meeting about plan for Admin Template 2018</a>
                                                    </h5>
                                                    <span class="time">02:00 PM</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--success">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Create new task for Dashboard</a>
                                                    </h5>
                                                    <span class="time">03:30 PM</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--danger js-load-item">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Meeting about plan for Admin Template 2018</a>
                                                    </h5>
                                                    <span class="time">10:00 AM</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--warning js-load-item">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Create new task for Dashboard</a>
                                                    </h5>
                                                    <span class="time">11:00 AM</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="au-task__footer">
                                            <button class="au-btn au-btn-load js-load-btn">load more</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--
                            <div class="col-lg-6">
                                <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
                                    <div class="au-card-title" style="background-image:url('images/bg-title-02.jpg');">
                                        <div class="bg-overlay bg-overlay--blue"></div>
                                        <h3>
                                            <i class="zmdi zmdi-comment-text"></i>New Messages</h3>
                                        <button class="au-btn-plus">
                                            <i class="zmdi zmdi-plus"></i>
                                        </button>
                                    </div>
                                    <div class="au-inbox-wrap js-inbox-wrap">
                                        <div class="au-message js-list-load">
                                            <div class="au-message__noti">
                                                <p>You Have
                                                    <span>2</span>

                                                    new messages
                                                </p>
                                            </div>
                                            <div class="au-message-list">
                                                <div class="au-message__item unread">
                                                    <div class="au-message__item-inner">
                                                        <div class="au-message__item-text">
                                                            <div class="avatar-wrap">
                                                                <div class="avatar">
                                                                    <img src="images/icon/avatar-02.jpg" alt="John Smith">
                                                                </div>
                                                            </div>
                                                            <div class="text">
                                                                <h5 class="name">John Smith</h5>
                                                                <p>Have sent a photo</p>
                                                            </div>
                                                        </div>
                                                        <div class="au-message__item-time">
                                                            <span>12 Min ago</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="au-message__item unread">
                                                    <div class="au-message__item-inner">
                                                        <div class="au-message__item-text">
                                                            <div class="avatar-wrap online">
                                                                <div class="avatar">
                                                                    <img src="images/icon/avatar-03.jpg" alt="Nicholas Martinez">
                                                                </div>
                                                            </div>
                                                            <div class="text">
                                                                <h5 class="name">Nicholas Martinez</h5>
                                                                <p>You are now connected on message</p>
                                                            </div>
                                                        </div>
                                                        <div class="au-message__item-time">
                                                            <span>11:00 PM</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="au-message__item">
                                                    <div class="au-message__item-inner">
                                                        <div class="au-message__item-text">
                                                            <div class="avatar-wrap online">
                                                                <div class="avatar">
                                                                    <img src="images/icon/avatar-04.jpg" alt="Michelle Sims">
                                                                </div>
                                                            </div>
                                                            <div class="text">
                                                                <h5 class="name">Michelle Sims</h5>
                                                                <p>Lorem ipsum dolor sit amet</p>
                                                            </div>
                                                        </div>
                                                        <div class="au-message__item-time">
                                                            <span>Yesterday</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="au-message__item">
                                                    <div class="au-message__item-inner">
                                                        <div class="au-message__item-text">
                                                            <div class="avatar-wrap">
                                                                <div class="avatar">
                                                                    <img src="images/icon/avatar-05.jpg" alt="Michelle Sims">
                                                                </div>
                                                            </div>
                                                            <div class="text">
                                                                <h5 class="name">Michelle Sims</h5>
                                                                <p>Purus feugiat finibus</p>
                                                            </div>
                                                        </div>
                                                        <div class="au-message__item-time">
                                                            <span>Sunday</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="au-message__item js-load-item">
                                                    <div class="au-message__item-inner">
                                                        <div class="au-message__item-text">
                                                            <div class="avatar-wrap online">
                                                                <div class="avatar">
                                                                    <img src="images/icon/avatar-04.jpg" alt="Michelle Sims">
                                                                </div>
                                                            </div>
                                                            <div class="text">
                                                                <h5 class="name">Michelle Sims</h5>
                                                                <p>Lorem ipsum dolor sit amet</p>
                                                            </div>
                                                        </div>
                                                        <div class="au-message__item-time">
                                                            <span>Yesterday</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="au-message__item js-load-item">
                                                    <div class="au-message__item-inner">
                                                        <div class="au-message__item-text">
                                                            <div class="avatar-wrap">
                                                                <div class="avatar">
                                                                    <img src="images/icon/avatar-05.jpg" alt="Michelle Sims">
                                                                </div>
                                                            </div>
                                                            <div class="text">
                                                                <h5 class="name">Michelle Sims</h5>
                                                                <p>Purus feugiat finibus</p>
                                                            </div>
                                                        </div>
                                                        <div class="au-message__item-time">
                                                            <span>Sunday</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="au-message__footer">
                                                <button class="au-btn au-btn-load js-load-btn">load more</button>
                                            </div>
                                        </div>
                                        <div class="au-chat">
                                            <div class="au-chat__title">
                                                <div class="au-chat-info">
                                                    <div class="avatar-wrap online">
                                                        <div class="avatar avatar--small">
                                                            <img src="images/icon/avatar-02.jpg" alt="John Smith">
                                                        </div>
                                                    </div>
                                                    <span class="nick">
                                                        <a href="#">John Smith</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="au-chat__content">
                                                <div class="recei-mess-wrap">
                                                    <span class="mess-time">12 Min ago</span>
                                                    <div class="recei-mess__inner">
                                                        <div class="avatar avatar--tiny">
                                                            <img src="images/icon/avatar-02.jpg" alt="John Smith">
                                                        </div>
                                                        <div class="recei-mess-list">
                                                            <div class="recei-mess">Lorem ipsum dolor sit amet, consectetur adipiscing elit non iaculis</div>
                                                            <div class="recei-mess">Donec tempor, sapien ac viverra</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="send-mess-wrap">
                                                    <span class="mess-time">30 Sec ago</span>
                                                    <div class="send-mess__inner">
                                                        <div class="send-mess-list">
                                                            <div class="send-mess">Lorem ipsum dolor sit amet, consectetur adipiscing elit non iaculis</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="au-chat-textfield">
                                                <form class="au-form-icon">
                                                    <input class="au-input au-input--full au-input--h65" type="text" placeholder="Type a message">
                                                    <button class="au-input-icon">
                                                        <i class="zmdi zmdi-camera"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            -->
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>Copyright © 2018 Colorlib. All rights reserved. Template by <a href="https://colorlib.com">Colorlib</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MAIN CONTENT-->
            <!-- END PAGE CONTAINER-->
        </div>

    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Jquery JS-->
    <script src="{{ URL::asset('vendor/jquery-3.2.1.min.js') }}"></script>
    <!-- Bootstrap JS-->
    <script src="{{ URL::asset('vendor/bootstrap-4.1/popper.min.js') }}"></script>
    <script src="{{ URL::asset('vendor/bootstrap-4.1/bootstrap.min.js') }}"></script>
    <!-- Vendor JS       -->
    <script src="{{ URL::asset('vendor/slick/slick.min.js') }}">
    </script>
    <script src="{{ URL::asset('vendor/wow/wow.min.js') }}"></script>
    <script src="{{ URL::asset('vendor/animsition/animsition.min.js') }}"></script>
    <script src="{{ URL::asset('vendor/bootstrap-progressbar/bootstrap-progressbar.min.js') }}">
    </script>
    <script src="{{ URL::asset('vendor/counter-up/jquery.waypoints.min.js') }}"></script>
    <script src="{{ URL::asset('vendor/counter-up/jquery.counterup.min.js') }}">
    </script>
    <script src="{{ URL::asset('vendor/circle-progress/circle-progress.min.js') }}"></script>
    <script src="{{ URL::asset('vendor/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ URL::asset('vendor/chartjs/Chart.bundle.min.js') }}"></script>
    <script src="{{ URL::asset('vendor/select2/select2.min.js') }}">
    </script>

    <!-- Main JS-->
    <script src="{{ URL::asset('js/main.js') }}"></script>
	
	<script>
    function dashboardDateShortcut(t) {
        tList = ['today', 'yesterday', 'this month', 'last month'];

        if (!tList.includes(t)) {
            t = 'today';
        }

        var date = new Date();
        var dd = String(date.getDate()).padStart(2, '0');
        var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = date.getFullYear();

        var dateFrom = '';
        var dateTo = '';

        if (t === 'today') {
            dateFrom = yyyy + '-' + mm + '-' + dd;
            dateTo = yyyy + '-' + mm + '-' + dd;
        } else if (t === 'yesterday') {
            dateFrom = yyyy + '-' + mm + '-' + (parseInt(dd) - 1).toString().padStart(2, '0');
            dateTo = yyyy + '-' + mm + '-' + (parseInt(dd) - 1).toString().padStart(2, '0');
        } else if (t == 'this month') {
            tmpD = new Date(parseInt(yyyy), mm, 0).getDate();
            tmpD = String(tmpD).padStart(2, '0');
            dateFrom = yyyy + '-' + mm + '-' + '01';
            dateTo = yyyy + '-' + mm + '-' + tmpD;
        } else if (t == 'last month') {
            var tmpY = parseInt(yyyy);
            var tmpM = parseInt(mm);
            if (tmpM === 1) {
                tmpM = 12;
                tmpY -= 1;
            } else {
                tmpM -= 1;
            }
            tmpD = new Date(tmpY, tmpM, 0).getDate();
            tmpD = String(tmpD).padStart(2, '0');
            dateFrom = tmpY + '-' + tmpM.toString().padStart(2, '0') + '-' + '01';
            dateTo = tmpY + '-' + tmpM.toString().padStart(2, '0') + '-' + tmpD;
        }

        $('#filter_date_from').val(dateFrom);
        $('#filter_date_to').val(dateTo);

        $('form#filter_data_form').submit();
    }
	</script>

</body>

</html>
<!-- end document-->
