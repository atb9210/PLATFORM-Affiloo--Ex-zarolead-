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
        <li class="breadcrumb-item"><a href="{{ route('tags') }}">Tags</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($action) }}</li>
        </ol>
        </nav>
        <h1 class="page-title">{{ ucfirst($action) }} Tag</h1>
        
        <div>
        	@if (!isset($success) || !$success)

            <?php
                if (!isset($id)) {
                    $id = '';
                }

                if (!isset($name)) {
                    $name = '';
                }

                if (!isset($info_bg_color)) {
                    $info_bg_color = '';
                }

                if (!isset($info_fg_color)) {
                    $info_fg_color = '';
                }

                $route = '';
                if ($action == 'create') {
                    $route = route('tag/create');
                } elseif ($action == 'update') {
                    $route = route('tag/edit', ['id' => $id]);
                }

            ?>

        	<form method="post" action="{{ $route }}">
        		@csrf

        		<div class="form-group">
                    Name*
                    <input type="text" name="name" placeholder="Tag name" value="{{ $name }}" required>
                </div>

                <div class="form-group">
                    <div class="bootstrap-select-wrapper">
                        <label>Background color</label>
                        <?php
                            $bg_color_options = array(
                                'red', 'green', 'blue', 'yellow', 'orange', 'white', 'black',
                            );
                            $bg_color_selected = array();

                            $one_is_selected = false;
                            foreach ($bg_color_options as $bg_color) {
                                if ($bg_color == $info_bg_color) {
                                    $one_is_selected = true;
                                    $bg_color_selected[$bg_color] = 'selected';
                                } else {
                                    $bg_color_selected[$bg_color] = '';
                                }
                            }

                            if (!$one_is_selected) {
                                $bg_color_selected['white'] = 'selected';
                            }
                        ?>
                        <select name="info_bg_color" style="margin-right: 20px;">
                            @foreach($bg_color_options as $bg_color)
                            <option value="{{ $bg_color }}" {{ $bg_color_selected[$bg_color] }}>{{ ucfirst($bg_color) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="bootstrap-select-wrapper">
                        <label>Foreground color</label>
                        <?php
                            $fg_color_options = array(
                                'red', 'green', 'blue', 'yellow', 'orange', 'white', 'black',
                            );
                            $fg_color_selected = array();

                            $one_is_selected = false;
                            foreach ($fg_color_options as $fg_color) {
                                if ($fg_color == $info_fg_color) {
                                    $one_is_selected = true;
                                    $fg_color_selected[$fg_color] = 'selected';
                                } else {
                                    $fg_color_selected[$fg_color] = '';
                                }
                            }

                            if (!$one_is_selected) {
                                $fg_color_selected['black'] = 'selected';
                            }
                        ?>
                        <select name="info_fg_color" style="margin-right: 20px;">
                            @foreach($fg_color_options as $fg_color)
                            <option value="{{ $fg_color }}" {{ $fg_color_selected[$fg_color] }}>{{ ucfirst($fg_color) }}</option>
                            @endforeach
                        </select>
                    </div>
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

            <div>1 row created/updated: tag "{{ $object->name }}"</div>

            @endif

            </textarea>
            @endif
        </div>
        
        
    </div>
</div>
@endsection