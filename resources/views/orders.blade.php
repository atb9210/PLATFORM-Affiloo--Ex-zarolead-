@extends('layouts/new')

@section('plugins_css')
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Orders</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title">Orders Table</h6>
            <p class="card-description">These are all the filtered orders.</p>
            <div class="table-responsive">
              <table id="dataTable" class="table">
                <thead>
                  <tr>
                    <th>Sent to network</th>
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
                    <tr>
                        <td></td>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->created_at }}</td>
                        <td>{{ $order->product }}</td>
                        <td>&euro; 10,00</td>
                        <td>{{ $order->name }}</td>
                        <td>{{ $order->phone }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->network }}</td>
                        <td></td>
                        <td>{{ $order->status->description }}</td>
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
@endsection

@section('custom_scripts')
<script src="{{ asset('assets/js/data-table.js') }}"></script>
@endsection
