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
        <li class="breadcrumb-item"><a href="{{ route('traffic-sources') }}">Traffic Sources</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($action) }}</li>
        </ol>
        </nav>
        <h1 class="page-title">{{ ucfirst($action) }} Traffic Source</h1>
        
        <div>
        	@if (!isset($success) || !$success)

            <?php
                if(!isset($id)) {
                    $id = '';
                }

                if(!isset($name)) {
                    $name = '';
                }

                $custom_params = array();
                if(isset($object)) {
                    $custom_params = $object->custom_params;
                }

                $max_custom_params = 10;

                $route = '';
                if ($action == 'create') {
                    $route = route('traffic-source/create');
                } elseif ($action == 'update') {
                    $route = route('traffic-source/edit', ['id' => $id]);
                }

            ?>

        	<form method="post" action="{{ $route }}">
        		@csrf

        		<div class="form-group">
                    Name*
                    <input type="text" name="name" placeholder="Traffic source name" value="{{ $name }}" required>
                </div>

                @for ($i = 0; $i < $max_custom_params; $i++)

                <?php
                    if ($i < count($custom_params)) {
                        $query_param = $custom_params[$i]['query_param'];
                        $token = $custom_params[$i]['token'];
                        $custom_param_name = $custom_params[$i]['name'];
                    } else {
                        $query_param = '';
                        $token = '';
                        $custom_param_name = '';
                    }
                ?>

                <div class="form-group row">
                    <div class="col-lg-3">
                        Custom {{ $i + 1 }}
                    </div>
                    <div class="col-lg-3">
                        Query Param:
                        <input type="text" name="query_param{{ $i }}" placeholder="Query Param" value="{{ $query_param }}">
                    </div>
                    <div class="col-lg-3">
                        Token:
                        [[<input type="text" name="token{{ $i }}" placeholder="Token" value="{{ $token }}">]]
                    </div>
                    <div class="col-lg-3">
                        Name:
                        <input type="text" name="name{{ $i }}" placeholder="Name" value="{{ $custom_param_name }}">
                    </div>
                </div>

                @endfor

	            <div>
		            * Campi obbligatori
		        </div>

                <input type="submit" class="btn btn-primary" value="{{ ucfirst($action) }}">

            </form>
            @else

            @if (isset($error) && $error != '')

            <div style="color: red;">{{ $error }}</div>

            @else

            <div>1 row created/updated: traffic source "{{ $object->name }}"</div>

            @endif

            </textarea>
            @endif
        </div>
        
        
    </div>
</div>
@endsection