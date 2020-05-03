<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Cool tracker</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link href="{{ url('/') }}/css/app.css" rel="stylesheet" type="text/css">
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
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
				width: 100%;
            }

            .title {
                font-size: 84px;
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
        <style>
            .page-title {
                margin-top: 30px;
            }
            .filters {
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <nav aria-label="breadcrumb" class="nav-breadcrumb">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders') }}">Ordini</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ordine #{{ $order->id }}</li>
                </ol>
                </nav>
                <h1 class="page-title">Ordine</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Data</th>
                            <th scope="col">ID</th>
							<th scope="col">Prodotto</th>
							<th scope="col">Commissione</th>
							<th scope="col">Network</th>
                            <th scope="col">Stato</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $order->created_at }}</td>
                            <td>{{ $order->id }}</td>
							<td>{{ $order->product }}</td>
							<td>{{ $order->commission }}</td>
							<td>{{ $order->network }}</td>
                            <td>{{ $order->status->description }}</td>
                        </tr>
                    </tbody>
                </table>
                <h3>Parametri</h3>
				<?php $order_info = json_decode($order->info); ?>
				<div style="width: auto;">
					@foreach ($order_info as $key => $value)
						<b>{{ $key }}</b>: {{ $value }}<br>
					@endforeach
				</div>
            </div>
        </div>
    </body>
</html>