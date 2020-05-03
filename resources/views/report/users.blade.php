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
        <li class="breadcrumb-item active" aria-current="page">Ordini</li>
        </ol>
        </nav>
        <h1 class="page-title">Ordini</h1>
        <div class="orders-filters">
            <div class="orders-filters-shortcuts">
				<button class="btn btn-primary" onclick="dashboardDateShortcut('today');">Oggi</button>
				<button class="btn btn-primary" onclick="dashboardDateShortcut('yesterday');">Ieri</button>
				<button class="btn btn-primary" onclick="dashboardDateShortcut('this month');">Questo Mese</button>
				<button class="btn btn-primary" onclick="dashboardDateShortcut('last month');">Mese Scorso</button>
            </div>
            <?php
            	$formRouteName = 'admin/report/users';
            ?>
            <form method="post" action="{{ route($formRouteName) }}" id="filter_data_form">
                @csrf
				Da: <input type="date" value="{{ $filter_date_from }}" id="filter_date_from" name="filter_date_from">
				A: <input type="date" value="{{ $filter_date_to }}" id="filter_date_to" name="filter_date_to">
                <input type="hidden" name="filter_email_id" value="{{ $filter_email_id }}">
                <input type="hidden" name="page_size" value="{{ $page_size }}">
				<input type="submit" class="btn btn-primary" value="Filtra">
            </form>
            <form method="post" action="{{ route($formRouteName) }}">
                @csrf
                <input type="hidden" name="filter_date_from" value="{{ $filter_date_from }}">
                <input type="hidden" name="filter_date_to" value="{{ $filter_date_to }}">
                <input type="hidden" name="page_size" value="{{ $page_size }}">
                <input type="text" name="filter_email_id" placeholder="Email/ID" value="{{ $filter_email_id }}">
                <input type="submit" value="Cerca utente" class="btn btn-primary">
            </form>
		</div>
        <div style="text-align: center;">
        	Risultati totali: {{ $users_count['Tutti']['count'] }} ({{ $page_size }} per pagina)
        </div>
        <div style="text-align: right; padding: 0px 15px;">
        	<form method="post" action="{{ route($formRouteName) }}">
                @csrf
                <input type="hidden" name="filter_date_from" value="{{ $filter_date_from }}">
                <input type="hidden" name="filter_date_to" value="{{ $filter_date_to }}">
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
        <div class="actions">
			<button class="btn btn-danger" onclick="deleteSelectedOrders();">Elimina</button>
        </div>
        <table class="table">
            <thead>
                <tr>
					<th scope="col">ID</th>
                    <th scope="col">Email</th>
                    <th scope="col">Leads</th>
					<th scope="col">Fatturato</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users_report as $user)
		            <?php
		            	setlocale(LC_MONETARY, 'it_IT');
		                $fmt = new NumberFormatter('it_IT', NumberFormatter::CURRENCY);
						$user['commissions'] = $fmt->formatCurrency($user['commissions'], "EUR");
		            ?>
                    <tr>
                        <td>{{ $user['id'] }}</td>
                        <td>{{ $user['email'] }}</td>
						<td>{{ $user['leads'] }}</td>
						<td>{{ $user['commissions'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function dashboardDateShortcut(t) {
    tList = ['today', 'yesterday', 'this month', 'last month'];

    if (!tList.includes(t)) {
        t = 'today';
    }

    var date = new Date();
    var dd = String(date.getDate()).padStart(2, '0');
    var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = date.getFullYear();

    var dateFrom = '';
    var dateTo = '';

    if (t === 'today') {
        dateFrom = yyyy + '-' + mm + '-' + dd;
        dateTo = yyyy + '-' + mm + '-' + dd;
    } else if (t === 'yesterday') {
        dateFrom = yyyy + '-' + mm + '-' + (parseInt(dd) - 1).toString().padStart(2, '0');
        dateTo = yyyy + '-' + mm + '-' + (parseInt(dd) - 1).toString().padStart(2, '0');
    } else if (t == 'this month') {
        tmpD = new Date(parseInt(yyyy), mm, 0).getDate();
        tmpD = String(tmpD).padStart(2, '0');
        dateFrom = yyyy + '-' + mm + '-' + '01';
        dateTo = yyyy + '-' + mm + '-' + tmpD;
    } else if (t == 'last month') {
        var tmpY = parseInt(yyyy);
        var tmpM = parseInt(mm);
        if (tmpM === 1) {
            tmpM = 12;
            tmpY -= 1;
        } else {
            tmpM -= 1;
        }
        tmpD = new Date(tmpY, tmpM, 0).getDate();
        tmpD = String(tmpD).padStart(2, '0');
        dateFrom = tmpY + '-' + tmpM.toString().padStart(2, '0') + '-' + '01';
        dateTo = tmpY + '-' + tmpM.toString().padStart(2, '0') + '-' + tmpD;
    }

    $('#filter_date_from').val(dateFrom);
    $('#filter_date_to').val(dateTo);

    $('form#filter_data_form').submit();
}
</script>
@endsection