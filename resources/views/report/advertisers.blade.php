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
                $formRouteName = 'admin/report/networks';
            ?>
            <form method="post" action="{{ route($formRouteName) }}" id="filter_data_form">
                @csrf
                Da: <input type="date" value="{{ $filter_date_from }}" id="filter_date_from" name="filter_date_from">
                A: <input type="date" value="{{ $filter_date_to }}" id="filter_date_to" name="filter_date_to">
                <input type="hidden" name="filter_product_name" value="{{ $filter_product_name }}">
                <input type="hidden" name="page_size" value="{{ $page_size }}">
                <input type="submit" class="btn btn-primary" value="Filtra">
            </form>
        </div>
        <div style="text-align: center;">
            Risultati totali: {{ $orders_count['Tutti']['count'] }} ({{ $page_size }} per pagina)
        </div>
        <div style="text-align: right; padding: 0px 15px;">
            <form method="post" action="{{ route($formRouteName) }}">
                @csrf
                <input type="hidden" name="filter_date_from" value="{{ $filter_date_from }}">
                <input type="hidden" name="filter_date_to" value="{{ $filter_date_to }}">
                <input type="hidden" name="filter_product_name" value="{{ $filter_product_name }}">
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
                    <th scope="col">Network</th>
                    <th scope="col">Leads</th>
                    <th scope="col">Fatturato</th>
                    <th scope="col">Attesa</th>
                    <th scope="col">Approvato</th>
                    <th scope="col">% Approvazioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($networksReport as $network)
                    <?php
                        setlocale(LC_MONETARY, 'it_IT');
                        $fmt = new NumberFormatter('it_IT', NumberFormatter::CURRENCY);

                        $approvato = 0.00;
                        $attesa = $network['commissions'] - $approvato;
                        $percApprovazioni = ($approvato / $network['commissions']) * 100.0;

                        $network['commissions'] = $fmt->formatCurrency($network['commissions'], "EUR");
                        $attesa = $fmt->formatCurrency($attesa, "EUR");
                        $approvato = $fmt->formatCurrency($approvato, "EUR");
                        $percApprovazioni .= " %";
                    ?>
                    <tr>
                        <td>{{ $network['network'] }}</td>
                        <td>{{ $network['leads'] }}</td>
                        <td>{{ $network['commissions'] }}</td>
                        <td>{{ $attesa }}</td>
                        <td>{{ $approvato }}</td>
                        <td>{{ $percApprovazioni }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
var orders = [<?php foreach($orders as $order) { echo $order->id . ", "; } ?>];
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
        var url = window.location.origin + '/order/' + order_id;
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