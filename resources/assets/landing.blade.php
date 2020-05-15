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
        <li class="breadcrumb-item"><a href="{{ route('landings') }}">Landings</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($action) }}</li>
        </ol>
        </nav>
        <h1 class="page-title">{{ ucfirst($action) }} Landing</h1>
        
        <div>
        	@if (!isset($success) || !$success)

            <?php
                if(!isset($id)) {
                    $id = '';
                }

                if (!isset($object)) {
                    $name = '';
                    $url = '';
                    $country = '';
                } else {
                    $name = $object->name;
                    $url = $object->url;
                    $country = $object->country;
                }

                $route = '';
                if ($action == 'create') {
                    $route = route('landing/create');
                } elseif ($action == 'update') {
                    $route = route('landing/edit', ['id' => $id]);
                }

            ?>

        	<form method="post" action="{{ $route }}">
        		@csrf

        		<div class="form-group">
	            	Name*
	                <input type="text" name="name" placeholder="Landing name" value="{{ $name }}" required>
	            </div>

                <div class="form-group">
                    <div class="bootstrap-select-wrapper">
                        <label>Product*</label>
                        <?php
                            $products = App\Product::all();

                            $product_selected = array();

                            $is_first = true;
                            foreach ($products as $product) {
                                if (isset($object) && $object->product_id != 0 && $object->product_id == $product->id) {
                                    $is_first = false;
                                    $product_selected[$product->id] = 'selected';
                                } elseif ($is_first) {
                                    $is_first = false;
                                    $product_selected[$product->id] = 'selected';
                                } else {
                                    $product_selected[$product->id] = '';
                                }
                            }
                        ?>
                        <select name="product_id" style="margin-right: 20px;" required>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $product_selected[$product->id] }}>{{ ucfirst($product->name) }}</option>
                            @endforeach
                        </select>
                    </div>
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
                    URL*
                    <input type="text" name="url" placeholder="http://..." value="{{ $url }}" required>
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

            <div>1 row created/updated: landing "{{ $object->name }}"</div>

            @endif

            </textarea>
            @endif
        </div>
        
        
    </div>
</div>
@endsection