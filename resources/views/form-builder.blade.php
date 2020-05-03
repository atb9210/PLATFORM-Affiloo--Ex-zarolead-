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
        <li class="breadcrumb-item active" aria-current="page">Create New</li>
        </ol>
        </nav>
        <h1 class="page-title">Form Builder</h1>
        
        <div>
        	@if (!isset($form_generated) || !$form_generated)

        	<form method="post" action="{{ route('form-builder') }}">
                @csrf

                <?php
                	$network = "";
                	if (isset($_GET['network'])) {
                		$network = $_GET['network'];
                	}
                ?>

	        	@if ($network == "")

	        	<h3>Choose Network</h3>
    			<a href="{{ route('form-builder') }}?network=worldfilia" class="btn btn-primary">Worldfilia</a><br>
    			<a href="{{ route('form-builder') }}?network=amsped" class="btn btn-primary">AMSped</a><br>
    			<a href="{{ route('form-builder') }}?network=affiliationpark" class="btn btn-primary">AffiliationPark</a><br>

    			@else

    			<input type="hidden" name="network" value="{{ $network }}">

    			<div class="form-group">
	            	Form name*
	                <input type="text" name="name" placeholder="Form name" value="" required>
	            </div>

	            <div class="form-group">
                    <div class="bootstrap-select-wrapper">
                        <label>Product*</label>
                        <?php
                            $products = App\Product::all();

                            $product_selected = array();

                            $is_first = true;
                            foreach ($products as $product) {
                            	if ($is_first) {
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
                                if ($is_first) {
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
                                if ($is_first) {
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
	                <input type="number" name="price" min="0.00" step="0.01" value="49.99" required/>
	            </div>

                <div class="form-group">
	                Commission*
	                <input type="number" name="commission" min="0.00" step="0.01" value="20.00" required />
	            </div>

	            <div class="form-group">
	                Sub Commission*
	                <input type="number" name="sub_commission" min="0.00" step="0.01" value="10.00" required />
	            </div>

	            <div class="form-group">
	                Quantity*
	                <input type="number" name="quantity" min="1" step="1" value="1" required />
	            </div>

	            <div class="form-group">
	            	Facebook Pixel
	                <input type="text" name="fb_pixel" placeholder="Ex. 2783719927728" value="">
	            </div>

	            <div class="form-group">
	            	Zeus Track
	                <input type="checkbox" name="zeus_track" value="true">
	            </div>

	            <hr>

        		@if ($network == "worldfilia")

	            <div class="form-group">
	                Source ID*
	                <input type="text" name="source_id" placeholder="Source ID" value="" required>
	            </div>

	            <div class="form-group">
	            	Json URL*
	                <input type="text" name="api_call_url" placeholder="Ex. http://example.com" value="" required>
	            </div>

        		@elseif ($network == "amsped")

	            <div class="form-group">
	                Product reference*
	                <input type="text" name="product_reference" placeholder="Ex. 32" value="" required>
	            </div>

	            <div class="form-group">
	                Sub ID
	                <input type="text" name="sub_id" placeholder="Ex. 32" value="">
	            </div>

	            <div class="form-group">
	            	Json URL*
	                <input type="text" name="api_call_url" placeholder="Ex. http://example.com" value="" required>
	            </div>

        		@elseif ($network == "affiliationpark")

	            <div class="form-group">
	                Offer ID*
	                <input type="text" name="offer_id" placeholder="Ex. d38d452ce473c02" value="" required>
	            </div>

	            <div class="form-group">
	                Sub ID
	                <input type="text" name="sub_id" placeholder="Ex. 32" value="">
	            </div>

	            <div class="form-group">
	            	Json URL*
	                <input type="text" name="api_call_url" placeholder="Ex. http://example.com" value="" required>
	            </div>

        		@endif

	            <div>
		            * Campi obbligatori
		        </div>

                <input type="submit" class="btn btn-primary" value="Build form">
            @endif

            </form>
            @else
            <textarea disabled="disabled" style="width: 90%; height: 60vh;">
            	{{ $form_html }}
            </textarea>
            @endif
        </div>
        
        
    </div>
</div>
@endsection