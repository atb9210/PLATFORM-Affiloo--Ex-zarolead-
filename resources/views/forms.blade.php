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
        <li class="breadcrumb-item active" aria-current="page">Forms</li>
        </ol>
        </nav>
        <h1 class="page-title">Forms</h1>
        <div class="orders-filters">
            <div class="orders-filters-shortcuts">
				<button class="btn btn-primary" onclick="dashboardDateShortcut('today');">Oggi</button>
				<button class="btn btn-primary" onclick="dashboardDateShortcut('yesterday');">Ieri</button>
				<button class="btn btn-primary" onclick="dashboardDateShortcut('this month');">Questo Mese</button>
				<button class="btn btn-primary" onclick="dashboardDateShortcut('last month');">Mese Scorso</button>
            </div>
            <?php
            	$formRouteName = 'forms';
            	if ($admin) {
            		$formRouteName = 'admin/' . $formRouteName;
            	}
            ?>
            <form method="post" action="{{ route($formRouteName) }}" id="filter_data_form">
                @csrf
				Da: <input type="date" value="{{ $filter_date_from }}" id="filter_date_from" name="filter_date_from">
				A: <input type="date" value="{{ $filter_date_to }}" id="filter_date_to" name="filter_date_to">
                <input type="hidden" name="page_size" value="{{ $page_size }}">
				<input type="submit" class="btn btn-primary" value="Filtra">
            </form>
		</div>
        <div style="text-align: center;">
        	Risultati totali: {{ $forms_count['Tutti']['count'] }} ({{ $page_size }} per pagina)
        </div>
        <div style="text-align: right; padding: 0px 15px;">
        	<form method="post" action="{{ route($formRouteName) }}">
                @csrf
                <input type="hidden" name="filter_date_from" value="{{ $filter_date_from }}">
                <input type="hidden" name="filter_date_to" value="{{ $filter_date_to }}">
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
        	<a href="{{ route('form-builder') }}" class="btn btn-primary">Create New</a>
			<button class="btn btn-danger" onclick="deleteSelectedOrders();">Delete</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                	<th scope="col"></th>
                    <th scope="col">ID</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Prodotto</th>
                    <th scope="col">User ID</th>
                    <!--<th scope="col">Traffic Source</th>
					<th scope="col">Network</th>-->
					<th scope="col">Visite</th>
					<th scope="col">Leads</th>
					<th scope="col">Spesa</th>
					<th scope="col">Fatturato</th>
					<th scope="col">Profitto</th>
					<th scope="col">ROI</th>
					<th scope="col">CR</th>
					<th scope="col">CPV</th>
					<th scope="col">EPV</th>
					<th scope="col">CPL</th>
					<th scope="col">Codice</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($forms as $form)
                	<?php
                		$visits = $form_stats[$form->id]['visits'];
                		$leads = $form_stats[$form->id]['leads'];
                		$total_commission = $form_stats[$form->id]['total_sub_commission'];
                		if (Auth::user()->hasPermission('admin') ||
                			Auth::user()->hasPermission('mediabuyer')) {
	                		$total_commission = $form_stats[$form->id]['total_commission'];
	                	}
                		$total_cost = $form_stats[$form->id]['total_cost'];

                		$profit = $total_commission - $total_cost;

                		// CR
                		$conv_rate = 0;
                		if ($leads == 0) {
                			$conv_rate = 0;
                		} elseif ($visits == 0) {
                			$conv_rate = 100;
                		} else {
	                		$conv_rate = ($leads / $visits) * 100;
	                	}

	                	// CPV
	                	$cost_per_visit = 0;
	                	if ($visits == 0) {
	                		$cost_per_visit = $total_cost;
	                	} else {
	                		$cost_per_visit = $total_cost / $visits;
	                	}

	                	// EPV
	                	$earnings_per_visit = 0;
	                	if ($visits == 0) {
	                		$earnings_per_visit = $total_commission;
	                	} else {
	                		$earnings_per_visit = $total_commission / $visits;
	                	}

	                	// CPL
	                	$cost_per_lead = 0;
	                	if ($leads == 0) {
	                		$cost_per_lead = $total_cost;
	                	} else {
	                		$cost_per_lead = $total_cost / $leads;
	                	}

	                	// ROI
	                	$roi = 0;
	                	if ($total_cost == 0 && $profit == 0) {
	                	} elseif ($total_cost == 0) {
                			$roi = 99999;
                		} else {
	                		$roi = ($profit / $total_cost) * 100;
	                	}

	                	$profitStyle = "font-weight: bold; color: black;";
	                	if ($roi < 0) {
	                		$profitStyle = "font-weight: bold; color: darkred;";
	                	} elseif ($roi > 0) {
	                		$profitStyle = "font-weight: bold; color: darkgreen;";
	                	}

	                	// Currency formatting
                		setlocale(LC_MONETARY, 'it_IT');
                		$fmt = new NumberFormatter('it_IT', NumberFormatter::CURRENCY);

						$total_commission = $fmt->formatCurrency($total_commission, "EUR");
						$total_cost = $fmt->formatCurrency($total_cost, "EUR");
						$profit = $fmt->formatCurrency($profit, "EUR");
						$cost_per_visit = $fmt->formatCurrency($cost_per_visit, "EUR");
						$earnings_per_visit = $fmt->formatCurrency($earnings_per_visit, "EUR");
						$cost_per_lead = $fmt->formatCurrency($cost_per_lead, "EUR");

						// Percentage formatting
						$conv_rate = round($conv_rate, 2);
						$roi = round($roi, 2);
                	?>
                    <tr>
                    	<td><input type="checkbox" id="select_{{ $form->id }}"></td>
                        <td>{{ $form->id }}</td>
                        <td style="font-weight: bold;">{{ $form->name }}</td>
                        <td>{{ $form->product->name }}</td>
                        <td>{{ $form->user_id }}</td>
						<!--<td>{{ $form->traffic_source }}</td>
						<td>{{ $form->network }}</td>-->
						<td>{{ $visits }}</td>
						<td>{{ $leads }}</td>
						<td>{{ $total_cost }}</td>
						<td>{{ $total_commission }}</td>
						<td style="{{ $profitStyle }}">{{ $profit }}</td>
						<td style="{{ $profitStyle }}">{{ $roi }} %</td>
						<td>{{ $conv_rate }} %</td>
						<td>{{ $cost_per_visit }}</td>
						<td>{{ $earnings_per_visit }}</td>
						<td>{{ $cost_per_lead }}</td>
						<td>
							<?php
								$form_stats_route = route('form', ['id' => $form->id]);
								$form_get_code_route = route('form_get_code', ['id' => $form->id]);
								$form_edit_route = route('form_edit', ['id' => $form->id]);
							?>
							<a href="{{ $form_stats_route }}" class="btn btn-primary" title="Report"><i class="fa fa-chart-bar"></i></a>
							<a href="{{ $form_get_code_route }}" class="btn btn-primary" title="View code"><i class="fa fa-code"></i></a>

							@if (Auth::user()->hasPermission('admin') ||
							Auth::user()->hasPermission('mediabuyer'))
							<a href="{{ $form_edit_route }}" class="btn btn-primary" title="Edit"><i class="fa fa-edit"></i></a>
							@endif
							<a href="#" class="btn btn-primary" title="Experiment"><i class="fa fa-flask"></i></a>
							<a href="#" class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></a>
						</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $forms->links() }}
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
var orders = [<?php foreach($forms as $form) { echo $form->id . ", "; } ?>];
var selectedOrders = [];
var deletedOrders = 0;

function log(s) {
	alert(s);
}

function makeRandomString(length) {
   var result           = '';
   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
	  result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}

function validatePhoneNumber(phoneNumber) {
	if (phoneNumber.length < 9) {
		return false;
	}
	var allowedChars = ['+', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ' '];
	for (i = 0; i < phoneNumber.length; i++) {
		if (!allowedChars.includes(phoneNumber[i])) {
			return false;
		}
	}
	return true;
}

function validateName(name) {
	return (name.split(" ").length >= 2);
}

function inputError(inputId, message) {
	$("#" + inputId + "Error").css("display", "block");
	$("#" + inputId + "Error").html(message);
	$("#" + inputId).css("border-color", "red");
}

var sessionId = '';
var orderId = 0;

function sendToNetwork(ID, IDProduct, quantity, name, phone, address) {
	
	if (!validateName(name)) {
		log("Nome scorretto (" + name + ")");
		return false;
	}
	if(!validatePhoneNumber(phone)) {
		log("Numero telefono scorretto (" + phone + ")");
		return false;
	}
	if(address.length < 1) {
		log("Indirizzo scorretto (" + phone + ")");
		return false;
	}

	sessionId = makeRandomString(32);
			
	// Log into bioness
	$.ajax({
		url: 'http://biogest.bioness.online/api/v.1.0/query.php?device=affiliator&action=login&sessionid=' + sessionId,
		method: "POST",
		dataType: "json",
		data: {
			User: 'Asraf',
			Password: 'Agke5VSxKFcX'
		},
		success: function(data) {
			if(data.result == true) {
				console.log("[i] LOGGED INTO BIONESS");
				
				var nameArray = name.split(" ");
				var clientName = nameArray[0];
				for (i = 1; i < nameArray.length - 1; i += 1) {
					clientName += " " + nameArray[i];
				}
				
				var clientSurame = nameArray[nameArray.length - 1];
				
				var data2 = {
					"ID": parseInt(ID),
					"IDProduct": parseInt(IDProduct),
					"NomeCliente": clientName,
					"CognomeCliente": clientSurame,
					"Telefono": phone,
					"QuantitaOriginale": quantity,
					
					
					// Not mandatory
					"Telefono2": "",
					"CittaCliente": "",
					"ProvinciaCliente": "",
					"CAPCliente": "",
					"IndirizzoCliente": address,
					"EmailCliente": "",
					"PrezzoFinale": "",
					"NoteGenerali": ""
				};
				
				console.log(data2);
				
				// Create new order
				$.ajax({
					url: 'http://biogest.bioness.online/api/v.1.0/query.php?device=affiliator&action=new-order&sessionid=' + sessionId,
					method: "POST",
					dataType: "json",
					data: data2,
					success: function(data) {
						var orderSent = false;

						console.log("[i] SENT TO BIONESS");
						console.log(data);
						if(data.result == true) {
							console.log("[i] BIONESS NEW ORDER CREATED");
							log("Ordine inviato!");
							orderSent = true;
						} else if (data.errorNumber == 14) {
							console.log("[!] ERROR: BIONESS NEW ORDER DATA ALREADY SENT");
							log("L'ordine era già presente.");
							orderSent = true;
						} else {
							console.log("[!] ERROR: BIONESS NEW ORDER NOT CREATED");
							log("Ordine non inviato! Risposta: " + data.errorMessage);
						}

						if (orderSent) {
							$.ajax({
								url: 'api/order_sent_to_network/' + ID,
								method: "GET",
								success: function() {
									location.reload();
								}
							});
						}
					},
					error: function(data) {
						console.log("[!] ERROR: BIONESS NEW ORDER NOT CREATED");
						console.log(data);
						log("Ordine non inviato!");
						log(data);
					}
				});
				
			} else {
				console.log("[i] CANNOT LOG INTO BIONESS");
				log("Errore login bioness!");
			}
			
		},
		error: function(data) {
			console.log("[!] ERROR: NOT SENT TO BIONESS");
			console.log(data);
		}
	});
	
	return false;
}

function getUrlParameter(sParam) {
	var sPageURL = decodeURIComponent(window.location.search.substring(1)),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;
	for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split('=');
		if (sParameterName[0] === sParam) {
			return sParameterName[1] === undefined ? true : sParameterName[1];
		}
	}
}

function ordersCustomDates() {
	window.location.href = '<?php echo URL::asset(''); ?>' + 'orders_custom/' + $('#fromDate').val().replace(/-/g, '') + '/' + $('#toDate').val().replace(/-/g, '');
}

function deleteSelectedOrders() {
	for (oIndex in orders) {
		var order = orders[oIndex];
		if ($('#select_' + order).is(":checked")) {
			selectedOrders.push(order);
		}
	}

	for (oIndex in selectedOrders) {
		var order_id = selectedOrders[oIndex];
		var url = window.location.origin + '/form/' + order_id;
		$.ajax({
			url: url,
			type: 'DELETE',
			data: {
                "id": order,
                "_method": 'DELETE',
                "_token": '{{ csrf_token() }}',
            },
			success: function(data) {
				deletedOrders += 1;
				if (deletedOrders == selectedOrders.length) {
					location.reload();
				}
			},
			error: function(data) {
				console.log(data);
			}
		});
	}
}
</script>

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
