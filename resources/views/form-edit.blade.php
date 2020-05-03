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
        <li class="breadcrumb-item"><a href="{{ route('forms') }}">Forms</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
        </nav>
        <h1 class="page-title">Form Edit</h1>
        
        <div>
        	@if (!isset($form_edited) || !$form_edited)

        	<form method="post" action="{{ route('form_edit', $form->id) }}">
                @csrf

                <?php
	        		$zeus_track_checked = '';
	        		if (isset($form->info['build_data']['zeus_track']) && $form->info['build_data']['zeus_track']) {
	        			$zeus_track_checked = 'checked';
	        		}

	        		$fb_pixel = '';
	        		if (isset($form->info['build_data']['fb_pixel'])) {
	        			$fb_pixel = $form->info['build_data']['fb_pixel'];
	        		}
	        	?>

	        	<input type="hidden" name="network" value="{{ $network }}">
        		<div class="form-group">
	            	Form name*
	                <input type="text" name="name" placeholder="Form name" value="{{ $form->name }}" required>
	            </div>

	            <div class="form-group">
                    <div class="bootstrap-select-wrapper">
                        <label>Product*</label>
                        <?php
                            $products = App\Product::all();

                            $product_selected = array();

                            $is_first = true;
                            foreach ($products as $product) {
                            	if ($form->product_id != 0 && $form->product_id == $product->id) {
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

	            @if (Auth::user()->hasPermission('admin'))
	            <div class="form-group">
                    <div class="bootstrap-select-wrapper">
                        <label>User ID*</label>
                        <?php
                            $users = App\User::all();

                            $user_selected = array();

                            $is_first = true;
                            foreach ($users as $user) {
                            	if ($form->user_id != 0 && $form->user_id == $user->id) {
                            		$is_first = false;
                            		$user_selected[$user->id] = 'selected';
                            	} elseif ($is_first) {
                            		$is_first = false;
                            		$user_selected[$user->id] = 'selected';
                            	} else {
                            		$user_selected[$user->id] = '';
                            	}
                            }
                        ?>
                        <select name="user_id" style="margin-right: 20px;" required>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $user_selected[$user->id] }}>{{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
	            @endif

	            <div class="form-group">
                    <div class="bootstrap-select-wrapper">
                        <label>Traffic Source*</label>
                        <?php
                            $traffic_sources = App\TrafficSource::all();

                            $ts_selected = array();

                            $is_first = true;
                            foreach ($traffic_sources as $traffic_source) {
                            	if ($form->traffic_source_id != 0 && $form->traffic_source_id == $traffic_source->id) {
                            		$is_first = false;
                            		$ts_selected[$traffic_source->id] = 'selected';
                            	} elseif ($is_first) {
                            		$is_first = false;
                            		$ts_selected[$traffic_source->id] = 'selected';
                            	} else {
                            		$ts_selected[$traffic_source->id] = '';
                            	}
                            }
                        ?>
                        <select name="traffic_source_id" style="margin-right: 20px;" required>
                            @foreach($traffic_sources as $traffic_source)
                            <option value="{{ $traffic_source->id }}" {{ $ts_selected[$traffic_source->id] }}>{{ ucfirst($traffic_source->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

	            <div class="form-group">
	                Price*
	                <input type="number" name="price" min="0.00" step="0.01" value="{{ $form->price }}" required/>
	            </div>

                <div class="form-group">
	                Commission*
	                <input type="number" name="commission" min="0.00" step="0.01" value="{{ $form->commission }}" required />
	            </div>

	            <div class="form-group">
	                Sub Commission*
	                <input type="number" name="sub_commission" min="0.00" step="0.01" value="{{ $form->sub_commission }}" required />
	            </div>

	            <div class="form-group">
	                Quantity*
	                <input type="number" name="quantity" min="1" step="1" value="{{ $form->quantity }}" required />
	            </div>

	            <div class="form-group">
	            	Facebook Pixel
	                <input type="text" name="fb_pixel" placeholder="Ex. 2783719927728" value="{{ $fb_pixel }}">
	            </div>

	            <div class="form-group">
	            	Zeus Track
	                <input type="checkbox" name="zeus_track" value="true" {{ $zeus_track_checked }}>
	            </div>

	            <hr>

	        	@if ($network == "worldfilia")

	        	<?php
	        		$api_call_url = '';
	        		if (isset($form->info['network']['api_call_url'])) {
	        			$api_call_url = $form->info['network']['api_call_url'];
	        		}

	        		$source_id = '';
	        		if (isset($form->info['build_data']['source_id'])) {
	        			$source_id = $form->info['build_data']['source_id'];
	        		}
	        	?>

	            <div class="form-group">
	                Source ID*
	                <input type="text" name="source_id" placeholder="Source ID" value="{{ $source_id }}" required>
	            </div>

	            <div class="form-group">
	            	Json URL*
	                <input type="text" name="api_call_url" placeholder="Ex. http://example.com" value="{{ $api_call_url }}" required>
	            </div>

        		@elseif ($network == "amsped")

        		<?php
	        		$api_call_url = '';
	        		if (isset($form->info['network']['api_call_url'])) {
	        			$api_call_url = $form->info['network']['api_call_url'];
	        		}

	        		$product_reference = '';
	        		if (isset($form->info['build_data']['product_reference'])) {
	        			$product_reference = $form->info['build_data']['product_reference'];
	        		}

	        		$sub_id = '';
	        		if (isset($form->info['build_data']['sub_id'])) {
	        			$sub_id = $form->info['build_data']['sub_id'];
	        		}
	        	?>

	            <div class="form-group">
	                Product reference*
	                <input type="text" name="product_reference" placeholder="Ex. 32" value="{{ $product_reference }}" required>
	            </div>

	            <div class="form-group">
	                Sub ID
	                <input type="text" name="sub_id" placeholder="Ex. 32" value="{{ $sub_id }}">
	            </div>

	            <div class="form-group">
	            	Json URL*
	                <input type="text" name="api_call_url" placeholder="Ex. http://example.com" value="{{ $api_call_url }}" required>
	            </div>

        		@elseif ($network == "affiliationpark")

        		<?php
	        		$api_call_url = '';
	        		if (isset($form->info['network']['api_call_url'])) {
	        			$api_call_url = $form->info['network']['api_call_url'];
	        		}

	        		$offer_id = '';
	        		if (isset($form->info['build_data']['offer_id'])) {
	        			$offer_id = $form->info['build_data']['offer_id'];
	        		}

	        		$sub_id = '';
	        		if (isset($form->info['build_data']['source_id'])) {
	        			$sub_id = $form->info['build_data']['sub_id'];
	        		}
	        	?>

	            <div class="form-group">
	                Offer ID*
	                <input type="text" name="offer_id" placeholder="Ex. d38d452ce473c02" value="{{ $offer_id }}" required>
	            </div>

	            <div class="form-group">
	                Sub ID
	                <input type="text" name="sub_id" placeholder="Ex. 32" value="{{ $sub_id }}">
	            </div>

	            <div class="form-group">
	            	Json URL*
	                <input type="text" name="api_call_url" placeholder="Ex. http://example.com" value="{{ $api_call_url }}" required>
	            </div>

        		@endif

	            <div>
		            * Campi obbligatori
		        </div>

                <input type="submit" class="btn btn-primary" value="Confirm">

            </form>
            @elseif ($error)
            	<span style="font-weight: bold; color:darkred;">{{ $message }}</span>
            @elseif (!$error)
            	<span style="font-weight: bold; color:darkgreen;">Form successfully modified!</span><br>
            	<a href="{{ route('forms') }}"><< Go to forms</a>
            @endif
        </div>
        
        
    </div>
</div>
@endsection