@extends('layouts/new')

@section('plugins_css')
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endsection

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Users</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title">Orders Table</h6>
            <p class="card-description"></p>
            <div class="mb-3">
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="filtersFormDiv">
                    <i class="link-icon" data-feather="filter"></i> Filters
                </button>
            </div>
            <div class="collapse filters" id="collapseExample">
                <form method="post" action="{{ route('test') }}">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-4">
                            From:
                            <div class="input-group date datepicker" id="filter_date_from_picker">
                                <input type="text" class="form-control" name="filter_date_from" value="{{ $filter_date_from }}">
                                <span class="input-group-addon bg-transparent"><i data-feather="calendar" class=" text-primary"></i></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            To:
                            <div class="input-group date datepicker" id="filter_date_to_picker">
                                <input type="text" class="form-control" name="filter_date_to" value="{{ $filter_date_to }}">
                                <span class="input-group-addon bg-transparent"><i data-feather="calendar" class=" text-primary"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            Status:
                            <select name="filter_order_status">
                                @foreach (App\OrderStatus::all() as $status)
                                <option value="{{ $status->id }}">{{ $status->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            Product:
                            <select class="selct2" name="filter_product">
                                @foreach (App\OrderStatus::all() as $status)
                                <option value="{{ $status->id }}">{{ $status->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <button class="btn btn-primary" type="submit" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="filtersFormDiv">
                                <i class="link-icon" data-feather="check"></i> Filters
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('orders') }}" class="btn btn-danger">
                                <i class="link-icon" data-feather="x"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
              <table id="dataTable" class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Commission</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Quantity</th>
                    <th>Network</th>
                    <th>Send</th>
                    <th>Status</th>
                    <th>Info</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                    <?php
                        $status_classes = [
                            App\OrderStatus::WAITING => "badge badge-info-muted",
                            App\OrderStatus::CONFIRMED => "badge badge-success",
                            App\OrderStatus::NOT_RESPONDING => "badge badge-info-muted",
                            App\OrderStatus::REFUSED => "badge badge-danger-muted text-white",
                            App\OrderStatus::NOT_SENT => "badge badge-danger",
                            App\OrderStatus::DOUBLE => "badge badge-warning",
                        ];

                        $commission = 0.00;
                        if (!Auth::user()->hasPermission('admin') && Auth::user()->hasPermission('affiliate')) {
                            if (isset($oder_info['sub_commission'])) {
                                $commission = $oder_info['sub_commission'];
                            }
                        } else {
                        	$commission = $order->commission;
                        }
                    ?>
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->created_at }}</td>
                        <td>{{ $order->product }}</td>
                        <td>&euro; {{ $commission }}</td>
                        <td>{{ $order->name }}</td>
                        <td>{{ $order->phone }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->network }}</td>
                        <td></td>
                        <td><span class="{{ $status_classes[$order->status->id] }}">{{ $order->status->description }}</span></td>
                        <td>Info</td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>
</div>
@endsection

@section('plugins_js')
<script src="{{ asset('assets/vendors/datatables.net/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
@endsection

@section('custom_scripts')
<script src="{{ asset('assets/js/data-table.js') }}"></script>
<script src="{{ asset('assets/js/custom/orders.js') }}"></script>
@endsection
