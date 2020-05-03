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
        <li class="breadcrumb-item"><a href="{{ route('products') }}">Products</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($action) }}</li>
        </ol>
        </nav>
        <h1 class="page-title">{{ ucfirst($action) }} Product</h1>
        
        <div>
        	@if (!isset($success) || !$success)

            <?php
                if(!isset($id)) {
                    $id = '';
                }

                if (!isset($object)) {
                    $name = '';
                    $country = '';
                } else {
                    $name = $object->name;
                    $country = $object->country;
                }

                $route = '';
                if ($action == 'create') {
                    $route = route('product/create');
                } elseif ($action == 'update') {
                    $route = route('product/edit', ['id' => $id]);
                }

            ?>

        	<form method="post" action="{{ $route }}">
        		@csrf

        		<div class="form-group">
	            	Name*
	                <input type="text" name="name" placeholder="Product name" value="{{ $name }}" required>
	            </div>

                <div class="form-group">
                    <div class="bootstrap-select-wrapper">
                        <label>Tag*</label>
                        <?php
                            $tags = App\Tag::all();

                            $tag_selected = array();

                            $is_first = true;
                            foreach ($tags as $tag) {
                                if (isset($object) && $object->tag_id != 0 && $object->tag_id == $tag->id) {
                                    $is_first = false;
                                    $tag_selected[$tag->id] = 'selected';
                                } elseif ($is_first) {
                                    $is_first = false;
                                    $tag_selected[$tag->id] = 'selected';
                                } else {
                                    $tag_selected[$tag->id] = '';
                                }
                            }
                        ?>
                        <select name="tag_id" style="margin-right: 20px;" required>
                            @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" {{ $tag_selected[$tag->id] }}>{{ ucfirst($tag->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="bootstrap-select-wrapper">
                        <label>Advertiser*</label>
                        <?php
                            $advertisers = App\Advertiser::all();

                            $adv_selected = array();

                            $is_first = true;
                            foreach ($advertisers as $advertiser) {
                                if (isset($object) && $object->advertiser_id != 0 && $object->advertiser_id == $advertiser->id) {
                                    $is_first = false;
                                    $adv_selected[$advertiser->id] = 'selected';
                                } elseif ($is_first) {
                                    $is_first = false;
                                    $adv_selected[$advertiser->id] = 'selected';
                                } else {
                                    $adv_selected[$advertiser->id] = '';
                                }
                            }
                        ?>
                        <select name="advertiser_id" style="margin-right: 20px;" required>
                            @foreach($advertisers as $advertiser)
                            <option value="{{ $advertiser->id }}" {{ $adv_selected[$advertiser->id] }}>{{ ucfirst($advertiser->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    Country
                    <input type="text" name="country" placeholder="Country" value="{{ $country }}">
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

            <div>1 row created/updated: product "{{ $object->name }}"</div>

            @endif

            </textarea>
            @endif
        </div>
        
        
    </div>
</div>
@endsection