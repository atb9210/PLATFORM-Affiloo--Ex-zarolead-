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
        <li class="breadcrumb-item active" aria-current="page">Utenti</li>
        </ol>
        </nav>
        <h1 class="page-title">Utenti</h1>
        <?php
        	$formRouteName = 'admin/users';
        ?>
        <div class="product-filters">
            <form method="post" action="{{ route($formRouteName) }}">
                @csrf
                <input type="hidden" name="page_size" value="{{ $page_size }}">
                <input type="text" name="filter_email_id" placeholder="Email/ID" value="{{ $filter_email_id }}">
                <input type="submit" class="btn btn-primary" value="Cerca utente">
            </form>
        </div>
        <div style="text-align: center;">
        	Risultati totali: {{ $rows_count['Tutti']['count'] }} ({{ $page_size }} per pagina)
        </div>
        <div style="text-align: right; padding: 0px 15px;">
        	<form method="post" action="{{ route($formRouteName) }}">
                @csrf
                <input type="hidden" name="filter_email_id" value="{{ $filter_email_id }}">
                Righe da mostrare:
                <select name="page_size" value="{{ $page_size }}">
                	<option value="{{ $page_size }}" selected disabled hidden>{{ $page_size }}</option>
                	<option value="10">10</option>
                	<option value="20">20</option>
                	<option value="50">50</option>
                	<option value="100">100</option>
                	<option value="250">250</option>
                	<option value="500">500</option>
                </select>
                <input type="submit" class="btn btn-primary" value="Aggiorna">
            </form>
        </div>
        <table class="table">
            <thead>
                <tr>
					<th scope="col">ID</th>
                    <th scope="col">Email</th>
                    <th scope="col">Permissions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
		            <?php
		            	setlocale(LC_MONETARY, 'it_IT');
		                $fmt = new NumberFormatter('it_IT', NumberFormatter::CURRENCY);
						$user['commissions'] = $fmt->formatCurrency($user['commissions'], "EUR");
		            ?>
                    <tr>
                        <td>{{ $user['id'] }}</td>
                        <td>{{ $user['email'] }}</td>
                        <td>
	                        <form method="post" action="{{ route($formRouteName) }}">
		                        @csrf
		                        <input type="hidden" name="page_size" value="{{ $page_size }}">
		                		<input type="hidden" name="filter_email_id" placeholder="Email/ID" value="{{ $filter_email_id }}">
		                        <input type="hidden" name="change_permission" value="1">
		                        <input type="hidden" name="user_id" value="{{ $user['id'] }}">
								<?php
									$permissions = [
                                        'admin',
                                        'mediabuyer',
                                        'affiliate',
                                        'advertiser',
                                        'accountant',
                                    ];
								?>

								@foreach ($permissions as $permission)
									<?php
										$checked = '';
										if ($user->hasPermission($permission)) {
											$checked = 'checked="checked"';
										}
									?>
									<div style="display: table; float: left; margin-right: 15px;">
									    <label style="display: table-row;" for="permission_{{ $permission }}">{{ $permission }}</label>
									    <input style="display: table-row; width: 100%;" type="checkbox" id="permission_{{ $permission }}" name="permission_{{ $permission }}" value="1" {{ $checked }} />         
									</div>
								@endforeach
								<input type="submit" class="btn btn-primary" value="Confirm">
							</form>
						</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection