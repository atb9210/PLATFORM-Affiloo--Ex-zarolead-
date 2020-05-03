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

    <?php

        if (!isset($action)) {
            $action = 'create';
        }

    ?>

    <div class="content">
        <nav aria-label="breadcrumb" class="nav-breadcrumb">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('advertisers') }}">Advertisers</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($action) }}</li>
        </ol>
        </nav>
        <h1 class="page-title">{{ ucfirst($action) }} Advertiser</h1>
        
        <div>
        	@if (!isset($success) || !$success)

            <?php
                if(!isset($id)) {
                    $id = '';
                }

                if(!isset($name)) {
                    $name = '';
                }

                $route = '';
                if ($action == 'create') {
                    $route = route('advertiser/create');
                } elseif ($action == 'update') {
                    $route = route('advertiser/edit', ['id' => $id]);
                }

            ?>

        	<form method="post" action="{{ $route }}">
        		@csrf

        		<div class="form-group">
	            	Name*
	                <input type="text" name="name" placeholder="Advertiser name" value="{{ $name }}" required>
	            </div>

	            <div>
		            * Campi obbligatori
		        </div>

                <input type="submit" class="btn btn-primary" value="{{ ucfirst($action) }}">

            </form>
            @else

            @if (isset($error) && $error != '')

            <div style="color: red;">{{ $error }}</div>

            @else

            <div>1 row created/updated: advertiser "{{ $object->name }}"</div>

            @endif

            </textarea>
            @endif
        </div>
        
        
    </div>
</div>
@endsection