@extends('layouts/new')

@section('plugins_css')
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
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
            <h6 class="card-title">Users Table</h6>
            <p class="card-description"></p>
            <div class="table-responsive">
              <table id="dataTable" class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Registration</th>
                    <th>Permissions</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>
								<?php
									$permissions = [
                                        'admin',
                                        'mediabuyer',
                                        'affiliate',
                                        'advertiser',
                                        'accountant',
                                    ];

                                    $permissions_init_value = [];
                                    foreach($permissions as $permission) {
                                        if ($user->hasPermission($permission)) {
                                            $arr = array(
                                                "id" => $permission,
                                                "value" => $permission,
                                            );
                                            $permissions_init_value[] = $arr;
                                        }
                                    }
								?>

                                <div class="form-group row">
                                    <div class="col-md-8">
                                        <select class="multiple-selection" name="permissions" multiple="multiple">
                                            @foreach ($permissions as $permission)
                                            <option value="{{ $permission }}">{{ $permission }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Confirm
                                        </button>
                                    </div>
                                </div>
                        </td>
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
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
@endsection

@section('custom_scripts')
<script src="{{ asset('assets/js/data-table.js') }}"></script>
<script src="{{ asset('assets/js/custom/users.js') }}"></script>
@endsection
