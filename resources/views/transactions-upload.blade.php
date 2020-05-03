@extends('layouts.app')

@section('content')
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
        <li class="breadcrumb-item"><a href="{{ route('invoices') }}">Invoices</a></li>
        <li class="breadcrumb-item active" aria-current="page">Transactions Upload</li>
        </ol>
        </nav>
        <h1 class="page-title">Transactions Upload</h1>
        
        <div>
        	<h3>Upload the .csv transactions file</h3>

            @if (!isset($transactions_uploaded_success) || !$transactions_uploaded_success)

            <form method="post" action="{{ route('invoices/transactions/upload') }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-top: 40px;">
                    <input type="file" name="file" accept=".csv" style="border: 1px solid #aaaaaa;" required>
                </div>
                <div style="margin-bottom: 20px;">* Solo in formato CSV</div>
                <button class="btn btn-primary" type="submit">Upload</button>
                @if (isset($error) && $error != '')
                <span style="color: red;">{{ $error }}</span>
                @endif
            </form>

            @else

            @if (isset($error) && $error != '')

            <div style="color: red;">{{ $error }}</div>

            @else

            <div>{{ $transactions_uploaded_count }} transactions uploaded!</div>

            @endif

            @endif
        </div>
        
        
    </div>
</div>
@endsection