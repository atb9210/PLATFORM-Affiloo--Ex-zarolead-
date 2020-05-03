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
        $formsRouteName = 'forms';
        $admin = Auth::user()->hasPermission('admin');
        if ($admin) {
            $formsRouteName = 'admin/' . $formsRouteName;
        }
    ?>

    <div class="content">
        <nav aria-label="breadcrumb" class="nav-breadcrumb">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route($formsRouteName) }}">Forms</a></li>
        <li class="breadcrumb-item active" aria-current="page">Form {{ $form->name }} (ID: {{ $form->id }})</li>
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
                $formRouteName = 'form';
            ?>
            <form method="post" action="{{ route($formRouteName, $form->id) }}" id="filter_data_form">
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
            <form method="post" action="{{ route($formRouteName, $form->id) }}">
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
            <button class="btn btn-primary" onclick="setAllCosts();">Update Spesa</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Data</th>
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
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $report)
                    <?php
                        $visits = $report->visits;
                        $leads = $report->leads;

                        $admin = Auth::user()->hasPermission('admin');
                        $total_commission = $report->info['total_sub_commission'];
                        if (Auth::user()->hasPermission('admin') ||
                            Auth::user()->hasPermission('mediabuyer')) {
                            $total_commission = $report->total_commission;
                        }
                        $total_cost = $report->total_cost;

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
                        if ($total_cost == 0) {
                            $roi = 99999;
                        } else {
                            $roi = ($profit / $total_cost) * 100;
                        }

                        // Currency formatting
                        setlocale(LC_MONETARY, 'it_IT');
                        $fmt = new NumberFormatter('it_IT', NumberFormatter::CURRENCY);

                        //$total_commission = $fmt->formatCurrency($total_commission, "EUR");
                        $total_commission = round($total_commission, 2);
                        //$profit = $fmt->formatCurrency($profit, "EUR");
                        $profit = round($profit, 2);
                        //$cost_per_visit = $fmt->formatCurrency($cost_per_visit, "EUR");
                        $cost_per_visit = round($cost_per_visit, 2);
                        //$earnings_per_visit = $fmt->formatCurrency($earnings_per_visit, "EUR");
                        $earnings_per_visit = round($earnings_per_visit, 2);
                        //$cost_per_lead = $fmt->formatCurrency($cost_per_lead, "EUR");
                        $cost_per_lead = round($cost_per_lead, 2);

                        // Percentage formatting
                        $conv_rate = round($conv_rate, 2);
                        $roi = round($roi, 2);

                        $profitStyle = "font-weight: bold; color: black;";
                        if ($roi < 0) {
                            $profitStyle = "font-weight: bold; color: darkred;";
                        } elseif ($roi > 0) {
                            $profitStyle = "font-weight: bold; color: darkgreen;";
                        }
                    ?>
                    <tr>
                        <td>{{ $report->day }}</td>
                        <td><span id="visits_{{ $report->id }}">{{ $visits }}</td>
                        <td><span id="leads_{{ $report->id }}">{{ $leads }}</td>
                        <td>
                            <input type="number" id="total_cost_{{ $report->id }}" class="cost_input" name="total_cost_{{ $report->id }}" min="0.00" step="0.01" value="{{ $total_cost }}" style="width: 30%;" />
                            <!--<button class="btn btn-primary" onclick="setTotalCost('{{ $report->id }}');">Update</button>-->
                        </td>
                        <td><span id="total_commission_{{ $report->id }}">{{ $total_commission }}</span> €</td>
                        <td style="{{ $profitStyle }}"><span id="profit_{{ $report->id }}">{{ $profit }}</span> €</td>
                        <td style="{{ $profitStyle }}"><span id="roi_{{ $report->id }}">{{ $roi }}</span> %</td>
                        <td><span id="conv_rate_{{ $report->id }}">{{ $conv_rate }}</span> %</td>
                        <td><span id="cost_per_visit_{{ $report->id }}">{{ $cost_per_visit }}</span> €</td>
                        <td><span id="earnings_per_visit_{{ $report->id }}">{{ $earnings_per_visit }}</span> €</td>
                        <td><span id="cost_per_lead_{{ $report->id }}">{{ $cost_per_lead }}</span> €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $reports->links() }}
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
var orders = [<?php foreach($reports as $report) { echo $report->id . ", "; } ?>];
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

function setTotalCost(report_id) {
    $.ajax({
        url:  window.location.origin + '/api/form_set_daily_cost',
        method: "POST",
        data: {
            "form_stats_id": report_id,
            "total_cost": $('#total_cost_' + report_id).val(),
            "_token": '{{ csrf_token() }}'
        },
        success: function(data) {
            console.log(data);

            if (data.result) {
                $('#total_cost_' + report_id).css('background-color', 'lightgreen');
                calculateStats(report_id);

                setTimeout(function(){ $('#total_cost_' + report_id).css('background-color', 'white'); }, 3000);
            } else {
                $('#total_cost_' + report_id).css('background-color', 'lightcoral');
            }
        },
        error: function(data) {
            console.log(data);

            $('#total_cost_' + report_id).css('background-color', 'lightcoral');
        }
    });
}

function setAllCosts() {
    $('.cost_input').each(function(index, obj){
        var reportId = obj.id.replace("total_cost_", "");
        setTotalCost(reportId);
    });
}

function calculateStats(id) {
    var visits = parseInt($('#visits_' + id).text());
    var leads = parseInt($('#leads_' + id).text());
    var total_cost = parseFloat($('#total_cost_' + id).val());
    var total_commission = parseFloat($('#total_commission_' + id).text());
    var profit = parseFloat($('#profit_' + id).text());
    var conv_rate = parseFloat($('#conv_rate_' + id).text());
    var cost_per_visit = parseFloat($('#cost_per_visit_' + id).text());
    var earnings_per_visit = parseFloat($('#earnings_per_visit_' + id).text());
    var cost_per_lead = parseFloat($('#cost_per_lead_' + id).text());

    /*
    console.log(visits);
    console.log(leads);
    console.log(total_cost);
    console.log(total_commission);
    console.log(profit);
    console.log(conv_rate);
    console.log(cost_per_visit);
    console.log(earnings_per_visit);
    console.log(cost_per_lead);
    */

    profit = total_commission - total_cost;

    // CR
    conv_rate = 0.0;
    if (leads == 0) {
        conv_rate = 0.0;
    } else if (visits == 0) {
        conv_rate = 100.0;
    } else {
        conv_rate = (parseFloat(leads) / parseFloat(visits)) * 100.0;
    }

    // CPV
    cost_per_visit = 0.0;
    if (visits == 0) {
        cost_per_visit = total_cost;
    } else {
        cost_per_visit = parseFloat(total_cost) / parseFloat(visits);
    }

    // EPV
    earnings_per_visit = 0.0;
    if (visits == 0) {
        earnings_per_visit = total_commission;
    } else {
        earnings_per_visit = parseFloat(total_commission) / parseFloat(visits);
    }

    // CPL
    cost_per_lead = 0.0;
    if (leads == 0) {
        cost_per_lead = total_cost;
    } else {
        cost_per_lead = parseFloat(total_cost) / parseFloat(leads);
    }

    // ROI
    roi = 0;
    if (total_cost == 0) {
        roi = 99999.0;
    } else {
        roi = (parseFloat(profit) / parseFloat(total_cost)) * 100;
    }

    // Format
    profit = profit.toFixed(2);
    roi = roi.toFixed(2);
    conv_rate = conv_rate.toFixed(2);
    cost_per_visit = cost_per_visit.toFixed(2);
    earnings_per_visit = earnings_per_visit.toFixed(2);
    cost_per_lead = cost_per_lead.toFixed(2);

    $('#profit_' + id).text(profit);
    $('#roi_' + id).text(roi);
    $('#conv_rate_' + id).text(conv_rate);
    $('#cost_per_visit_' + id).text(cost_per_visit);
    $('#earnings_per_visit_' + id).text(earnings_per_visit);
    $('#cost_per_lead_' + id).text(cost_per_lead);
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