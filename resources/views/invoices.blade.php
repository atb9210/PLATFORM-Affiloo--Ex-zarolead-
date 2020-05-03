@extends('layouts.app')

<script>
window.Laravel = <?php echo json_encode([
'csrfToken' => csrf_token(),
]); ?>
</script>

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
        <li class="breadcrumb-item active" aria-current="page">Invoices</li>
        </ol>
        </nav>
        <h1 class="page-title">Invoices</h1>
        <div class="orders-filters">
            <div class="orders-filters-shortcuts" style="margin-bottom: 20px;">
				<button class="btn btn-primary" onclick="dashboardDateShortcut('today');">Oggi</button>
				<button class="btn btn-primary" onclick="dashboardDateShortcut('yesterday');">Ieri</button>
				<button class="btn btn-primary" onclick="dashboardDateShortcut('this month');">Questo Mese</button>
				<button class="btn btn-primary" onclick="dashboardDateShortcut('last month');">Mese Scorso</button>
            </div>
            <?php
            	$formRouteName = 'invoices';
            	if ($admin) {
            		$formRouteName = 'admin/' . $formRouteName;
            	}
            ?>
            <form method="post" action="{{ route($formRouteName) }}" id="filter_form">
                @csrf
                <input type="hidden" name="page_size" value="{{ $page_size }}">
                <div class="form-group">
    				Da: <input type="date" value="{{ $filter_date_from }}" id="filter_date_from" name="filter_date_from">
    				A: <input type="date" value="{{ $filter_date_to }}" id="filter_date_to" name="filter_date_to">
                </div>
                <div class="form-group">
                    Min: € <input name="filter_min_amount" type="number" min="0.00" step="0.01" value="{{ $filter_min_amount }}"/>
                    Max: € <input name="filter_max_amount" type="number" min="0.00" step="0.01" value="{{ $filter_max_amount }}"/>
                </div>
                <div class="form-group">
                    <div class="bootstrap-select-wrapper">
                        <label>Uploaded</label>
                        <?php
                            $selected_none = '';
                            $selected_yes = '';
                            $selected_no = '';
                            if ($filter_uploaded == 'yes') {
                                $selected_yes = "selected";
                            } elseif ($filter_uploaded == 'no') {
                                $selected_no = "selected";
                            } else {
                                $selected_none = "selected";
                            }
                        ?>
                        <select name="filter_uploaded" style="margin-right: 20px;">
                            <option value="all" {{ $selected_none }}>All</option>
                            <option value="yes" {{ $selected_yes }}>Yes</option>
                            <option value="no" {{ $selected_no }}>No</option>
                        </select>
                        <label>Transaction</label>
                        <?php
                            $selected_none = '';
                            $selected_yes = '';
                            $selected_no = '';
                            if ($filter_transaction == 'yes') {
                                $selected_yes = "selected";
                            } elseif ($filter_transaction == 'no') {
                                $selected_no = "selected";
                            } else {
                                $selected_none = "selected";
                            }
                        ?>
                        <select name="filter_transaction">
                            <option value="all" {{ $selected_none }}>All</option>
                            <option value="yes" {{ $selected_yes }}>Yes</option>
                            <option value="no" {{ $selected_no }}>No</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="bootstrap-select-wrapper">
                        <label>Status</label>
                        <?php
                            $selected_none = '';
                            $selected_paid = '';
                            $selected_failed = '';
                            if ($filter_status == 'paid') {
                                $selected_paid = "selected";
                            } elseif ($filter_status == 'failed') {
                                $selected_failed = "selected";
                            } else {
                                $selected_none = "selected";
                            }
                        ?>
                        <select name="filter_status" style="margin-right: 20px;">
                            <option value="" {{ $selected_none }}>All</option>
                            <option value="paid" {{ $selected_paid }}>Paid</option>
                            <option value="failed" {{ $selected_failed }}>Failed</option>
                        </select>
                        <label>Auto invoice</label>
                        <?php
                            $selected_none = '';
                            $selected_yes = '';
                            $selected_no = '';
                            if ($filter_auto_invoice == 'yes') {
                                $selected_yes = "selected";
                            } elseif ($filter_auto_invoice == 'no') {
                                $selected_no = "selected";
                            } else {
                                $selected_none = "selected";
                            }
                        ?>
                        <select name="filter_auto_invoice">
                            <option value="all" {{ $selected_none }}>All</option>
                            <option value="yes" {{ $selected_yes }}>Yes</option>
                            <option value="no" {{ $selected_no }}>No</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div style="margin-bottom: 20px;">
        				<input type="submit" class="btn btn-primary" value="Filtra">
                        <a href="{{ route($formRouteName) }}" class="btn btn-danger">
                            <i class="fa fa-trash"></i> Azzera Filtri
                        </a>
                    </div>
                    <div>
                        <a class="btn btn-primary" onclick="downloadInvoicesSummary();">
                            <i class="fa fa-download"></i> Download Invoices Summary
                        </a>
                        <a class="btn btn-warning" onclick="downloadAutoInvoicesSummary();">
                            <i class="fa fa-download"></i> Download Auto Invoices Summary
                        </a>
                    </div>
                </div>
            </form>
            <form action="{{ route('invoices/summary/download') }}" method="post" target="_blank" id="download_invoices_summary_form" style="display: none;">
                @csrf
                <input type="hidden" name="filter_date_from" id="invoices_summary_date_from" value="{{ $filter_date_from }}">
                <input type="hidden" name="filter_date_to" id="invoices_summary_date_to" value="{{ $filter_date_to }}">
            </form>
            <form action="{{ route('auto-invoices/summary/download') }}" method="post" target="_blank" id="download_auto_invoices_summary_form" style="display: none;">
                @csrf
                <input type="hidden" name="filter_date_from" id="auto_invoices_summary_date_from" value="{{ $filter_date_from }}">
                <input type="hidden" name="filter_date_to" id="auto_invoices_summary_date_to" value="{{ $filter_date_to }}">
                <input type="hidden" name="filter_auto_invoice" value="yes">
            </form>
		</div>
        <div style="text-align: center;">
        	Risultati totali: {{ $invoices_count['Tutti']['count'] }} ({{ $page_size }} per pagina)<br>
            Total amount: € {{ $invoices_count['Tutti']['total_amount'] }}
        </div>
        <div style="text-align: right; padding: 0px 15px;">
        	<form method="post" action="{{ route($formRouteName) }}">
                @csrf
                <input type="hidden" name="filter_date_from" value="{{ $filter_date_from }}">
                <input type="hidden" name="filter_date_to" value="{{ $filter_date_to }}">
                <input type="hidden" name="filter_min_amount" value="{{ $filter_min_amount }}">
                <input type="hidden" name="filter_max_amount" value="{{ $filter_max_amount }}">
                <input type="hidden" name="filter_uploaded" value="{{ $filter_uploaded }}">
                <input type="hidden" name="filter_transaction" value="{{ $filter_transaction }}">
                <input type="hidden" name="filter_status" value="{{ $filter_status }}">
                <input type="hidden" name="filter_auto_invoice" value="{{ $filter_auto_invoice }}">
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
            <form action="{{ route('invoices/download') }}" method="post" target="_blank" id="download_multiple_invoices_form" style="display: none;">
                @csrf
                <input type="hidden" id="id_array" name="id_array" value="">
            </form>
            <button class="btn btn-primary" onclick="downloadMultipleInvoices();">Download as Zip</button>
            <button class="btn btn-warning" onclick="setAutoInvoices();">Tag auto invoice</button>
            <button class="btn btn-danger" onclick="deleteSelectedInvoices();">Elimina</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" onclick="selectAllInvoices();"></th>
                    <th scope="col">ID</th>
                    <th scope="col">Uploaded</th>
                    <th scope="col">Transaction</th>
                    @if ($admin)
                    	<!--<th scope="col">User ID</th>-->
                    @endif
                    <th scope="col">Date</th>
					<th scope="col">Transaction ID</th>
                    <th scope="col">Number</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Currency</th>
					<th scope="col">Status</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                	<?php
                        $currencies = array(
                            'eur' => '€',
                            'usd' => '$',
                        );

                        $uploaded = $invoice->uploaded ? 'Yes' : 'No';
                        $transaction = $invoice->transaction ? 'Yes' : 'No';

						setlocale(LC_MONETARY, 'it_IT');
						$fmt = new NumberFormatter('it_IT', NumberFormatter::CURRENCY);
						$amount = $fmt->formatCurrency($invoice->amount, $invoice->currency);

                        $trStyle="";
                        if ($invoice->status != 'paid') {
                            $trStyle="background-color: rgba(139, 0, 0, 0.7); color: white;";
                        }
                        if (isset($invoice->info['auto_invoice']) && $invoice->info['auto_invoice']) {
                            $trStyle="background-color: rgba(255, 165, 0, 0.7);";
                        }
                	?>
                    <tr style="{{ $trStyle }}">
                        <td><input type="checkbox" id="select_{{ $invoice->id }}"></td>
                        <td>{{ $invoice->id }}</td>
                        <td>{{ $uploaded }}</td>
                        <td>{{ $transaction }}</td>
                        @if ($admin)
                        	<!--<td>{{ $invoice->user_id }}</td>-->
                        @endif
                        <td>{{ $invoice->invoice_date }}</td>
						<td>{{ $invoice->transaction_id }}</td>
                        <td>{{ $invoice->number }}</td>
						<td>{{ $amount }}</td>
                        <td>{{ $invoice->currency }} ({{ $currencies[$invoice->currency] }})</td>
						<td>{{ $invoice->status }}</td>
                        <td>
                            <?php
                                $invoice_download_route = route('invoice_download', ['id' => $invoice->id]);

                                $download_button_class = 'btn-primary';
                                $download_button_title = 'Download';
                                if(!Storage::exists($invoice->getStoragePathname())) {
                                    $download_button_class = 'btn-secondary';
                                    $invoice_download_route = '';
                                    $download_button_title = 'File not stored';
                                }
                            ?>
                            <a href="{{ $invoice_download_route }}" class="btn {{ $download_button_class }}" title="{{ $download_button_title }}"><i class="fa fa-file-pdf"></i></a>
                            <a href="#" class="btn btn-danger" onclick="deleteInvoice({{ $invoice->id }});" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $invoices->links() }}
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
var invoices = [<?php foreach($invoices as $invoice) { echo $invoice->id . ", "; } ?>];
var selectedInvoices = [];
var deletedInvoices = 0;
var generatedAutoInvoices = 0;
var allInvoicesSelected = false;

function refreshSelectedInvoices() {
    selectedInvoices = [];
    for (iIndex in invoices) {
        var invoice = invoices[iIndex];
        if ($('#select_' + invoice).is(":checked")) {
            selectedInvoices.push(invoice);
        }
    }
}

function setAutoInvoices() {
    refreshSelectedInvoices();

    for (iIndex in selectedInvoices) {
        var invoice_id = selectedInvoices[iIndex];
        var url = window.location.origin + '/invoice/' + invoice_id + '/set_auto_invoice';
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                "id": invoice_id,
                "_token": '{{ csrf_token() }}',
            },
            success: function(data) {
                generatedAutoInvoices += 1;
                if (generatedAutoInvoices == selectedInvoices.length) {
                    location.reload();
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
}

function deleteInvoice(invoice_id) {
    var url = window.location.origin + '/invoice/' + invoice_id;
    $.ajax({
        url: url,
        type: 'DELETE',
        data: {
            "id": invoice_id,
            "_method": 'DELETE',
            "_token": '{{ csrf_token() }}',
        },
        success: function(data) {
            location.reload();
        },
        error: function(data) {
            console.log(data);
        }
    });
}

function deleteSelectedInvoices() {
    refreshSelectedInvoices();

    for (iIndex in selectedInvoices) {
        var invoice_id = selectedInvoices[iIndex];
        var url = window.location.origin + '/invoice/' + invoice_id;
        $.ajax({
            url: url,
            type: 'DELETE',
            data: {
                "id": invoice_id,
                "_method": 'DELETE',
                "_token": '{{ csrf_token() }}',
            },
            success: function(data) {
                deletedInvoices += 1;
                if (deletedInvoices == selectedInvoices.length) {
                    location.reload();
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
}

function selectAllInvoices() {
    if (!allInvoicesSelected) {
        for (iIndex in invoices) {
            var invoice = invoices[iIndex];
            $('#select_' + invoice).prop("checked", true);
        }
        allInvoicesSelected = true;
    } else {
        for (iIndex in invoices) {
            var invoice = invoices[iIndex];
            $('#select_' + invoice).prop("checked", false);
        }
        allInvoicesSelected = false;
    }
}

function downloadMultipleInvoices() {
    refreshSelectedInvoices();

    if (selectedInvoices.length == 0) {
        return;
    }

    $('#id_array').val(JSON.stringify(selectedInvoices));
    $('#download_multiple_invoices_form').submit();
}

function downloadInvoicesSummary() {
    var date_from = $('#filter_date_from').val();
    var date_to = $('#filter_date_to').val();

    $('#invoices_summary_date_from').val(date_from);
    $('#invoices_summary_date_to').val(date_to);

    $('#download_invoices_summary_form').submit();
}

function downloadAutoInvoicesSummary() {
    var date_from = $('#filter_date_from').val();
    var date_to = $('#filter_date_to').val();

    $('#auto_invoices_summary_date_from').val(date_from);
    $('#auto_invoices_summary_date_to').val(date_to);

    $('#download_auto_invoices_summary_form').submit();
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

    $('#filter_form').submit();
}
</script>
@endsection