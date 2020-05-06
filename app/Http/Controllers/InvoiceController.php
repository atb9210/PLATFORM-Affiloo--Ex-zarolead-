<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Invoice;
use App\Http\Resources\Invoice as InvoiceResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller {
    private $permissions = [
        'admin_view' => ['admin', 'accountant'],
        'view' => ['admin', 'mediabuyer', 'affiliate', 'accountant'],
        'upload' => ['admin', 'affiliate', 'mediabuyer', 'accountant'],
        'upload_transactions' => ['admin', 'accountant'],
    ];

    private $TRANSACTION_ID_KEYWORDS = [
        'eng' => 'Transaction ID',
        'it' => 'ID Transazione',
    ];

    private $NUMBER_KEYWORDS = [
        'eng' => 'Invoice # ',
        'it' => 'Num. fattura ',
    ];

    private $MONTHS = [
        'eng' => [
            'Jan' => 1,
            'Feb' => 2,
            'Mar' => 3,
            'Apr' => 4,
            'May' => 5,
            'Jun' => 6,
            'Jul' => 7,
            'Aug' => 8,
            'Sep' => 9,
            'Oct' => 10,
            'Nov' => 11,
            'Dec' => 12,
        ],
        'it' => [
            'gen' => 1,
            'feb' => 2,
            'mar' => 3,
            'apr' => 4,
            'mag' => 5,
            'giu' => 6,
            'lug' => 7,
            'ago' => 8,
            'set' => 9,
            'ott' => 10,
            'nov' => 11,
            'dic' => 12,
        ],
    ];

    private $STATUSES = [
        'eng' => [
            'Facebook Paid' => 'paid',
            'Facebook Failed' => 'failed',
        ],
        'it' => [
            'Facebook Pagamento effettuato' => 'paid',
            'Facebook Operazione non riuscita' => 'failed',
        ],
    ];

    private $CURRENCIES = [
        'eur' => '€',
        'usd' => '$',
    ];

    private $TRANSACTION_DATE_COL_NUMBER = 1;
    private $TRANSACTION_DESCRIPTION_COL_NUMBER = 8;
    private $TRANSACTION_AMOUNT_COL_NUMBER = 9;
    private $TRANSACTION_CURRENCY_COL_NUMBER = 10;

    private function permissionGuard($action = 'view') {
        $result = false;
        foreach ($this->permissions[$action] as $permission) {
            if (Auth::user()->hasPermission($permission)) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    private function fetchRawData($rows) {
    	$rawData = array(
    		'language' => '',
    		'invoice_date' => '',
    		'transaction_id' => '',
            'number' => '',
    		'status' => '',
    		'amount' => '',
            'currency' => '',
    	);

    	$check_language_row_number = 0;
		$date_row_number = 3;
		$transaction_id_row_number = 6;
        $number_row_number = -1;
		$status_row_number = 10;
		$amount_row_number = 11;

    	// Get language
    	$lang = 'unknown';
		$words = explode(' ', $rows[$check_language_row_number]);
    	if ($words[0] == 'Invoice' || $words[0] == 'Receipt') {
    		$lang = 'eng';
    	} else if ($words[0] == 'Fattura' || $words[0] == 'Ricevuta') {
    		$lang = 'it';
    	}
    	$rawData['language'] = $lang;

    	// Get invoice date
    	$rawData['invoice_date'] = $rows[$date_row_number];

        for ($i = $date_row_number; $i < count($rows); $i++) {
            if (strpos($rows[$i], $this->TRANSACTION_ID_KEYWORDS[$lang]) !== false) {
                $transaction_id_row_number = $i + 1;
                continue;
            }

            if (strpos($rows[$i], $this->NUMBER_KEYWORDS[$lang]) !== false) {
                $number_row_number = $i;
                continue;
            }

            foreach ($this->STATUSES[$lang] as $key => $value) {
                if (strpos($rows[$i], $key) !== false) {
                    $status_row_number = $i;
                    $amount_row_number = $i + 1;
                    break;
                }
            }
        }

    	// Get invoice transaction id and number
    	$rawData['transaction_id'] = $rows[$transaction_id_row_number];
        $rawData['number'] = '';
        if ($number_row_number >= 0) {
            $rawData['number'] = $rows[$number_row_number];
        }

    	// Get invoice status
    	$rawData['status'] = $rows[$status_row_number];

    	// Get invoice amount
    	$rawData['amount'] = $rows[$amount_row_number];

    	return $rawData;
    }

    private function processRawData($rawData) {
        /*
        echo "<pre>" . var_export($rawData, true) . "</pre>";
        exit();
        */

    	$invoice = array(
    		'language' => '',
    		'invoice_date' => '',
    		'transaction_id' => '',
            'number' => '',
    		'status' => '',
    		'amount' => '',
            'currency' => '',
    	);

    	$invoice['language'] = $rawData['language'];

    	if ($rawData['language'] == 'eng') {
    		// Process data
    		$words = explode(' ', str_replace(',', '', $rawData['invoice_date']));
    		$d = $words[1];
    		$m = $this->MONTHS[$rawData['language']][$words[0]];
    		$y = $words[2];

    		$t = explode(':', $words[3]);
    		$h = $t[0];
    		$min = $t[1];
    		if ($words[4] == 'PM') {
                if ($h == 12) {
                    $h = 0;
                } else {
                    $h += 12;
                }
    		}

    		// 0 padding
    		if (strlen($d) == 1) {
    			$d = '0' .$d;
    		}
    		if (strlen($m) == 1) {
    			$m = '0' .$m;
    		}
    		if (strlen($h) == 1) {
    			$h = '0' .$h;
    		}
    		if (strlen($min) == 1) {
    			$min = '0' .$min;
    		}

    		$invoice['invoice_date'] = $y . '/' . $m . '/' . $d . ' ' . $h . ':' . $min . ':00';
    	} else if ($rawData['language'] == 'it') {
    		// Process data
    		$words = explode(' ', str_replace(',', '', $rawData['invoice_date']));
    		$d = $words[0];
    		$m = $this->MONTHS[$rawData['language']][$words[1]];
    		$y = $words[2];

    		$t = explode(':', $words[3]);
    		$h = $t[0];
    		$min = $t[1];

    		// 0 padding
    		if (strlen($d) == 1) {
    			$d = '0' .$d;
    		}
    		if (strlen($m) == 1) {
    			$m = '0' .$m;
    		}
    		if (strlen($h) == 1) {
    			$h = '0' .$h;
    		}
    		if (strlen($min) == 1) {
    			$min = '0' .$min;
    		}

    		$invoice['invoice_date'] = $y . '/' . $m . '/' . $d . ' ' . $h . ':' . $min . ':00';

            $rawData['amount'] = str_replace(',', '.', str_replace('.', '', $rawData['amount']));
    	}

    	$words = explode(' ', $rawData['transaction_id']);
    	$invoice['transaction_id'] = $words[count($words) - 1];

        if ($rawData['number'] != '') {
            $invoice['number'] = explode($this->NUMBER_KEYWORDS[$invoice['language']], $rawData['number'])[1];
        }

        $currency = '';
        foreach ($this->CURRENCIES as $key => $value) {
            if (strpos($rawData['amount'], $value) !== false) {
                $currency = $key;
                break;
            }
        }
        $invoice['currency'] = $currency;

        $invoice['status'] = $this->STATUSES[$rawData['language']][$rawData['status']];

        $rawAmount = str_replace($this->CURRENCIES[$currency], '', $rawData['amount']);
        $invoice['amount'] = floatval($rawAmount);

    	return $invoice;
    }

    public function uploadIndex() {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('upload')) {
            return redirect()->route('dashboard');
        }

    	return view('invoice-upload');
    }

    public function upload(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('upload')) {
            return redirect()->route('dashboard');
        }

    	$files = $request->file('files');

    	$invoices_uploaded_count = 0;
    	$invoices_uploaded = array();
		if($request->hasFile('files')) {
			foreach ($files as $file) {

				if ($file->getClientOriginalExtension() != 'pdf') {
					continue;
				}

				$parser = new \Smalot\PdfParser\Parser();
				$pdf = $parser->parseFile($file->getPathname());
				$text = $pdf->getText();
				$rows = explode("\n", $text);

                /*
                for($i = 0; $i < count($rows); $i += 1) {
                    echo $i . ') ' . $rows[$i] . "<br>";
                }
                exit();
                */

				$invoice = $this->processRawData($this->fetchRawData($rows));
                $invoice['original_file_name'] = $file->getClientOriginalName();

				// Check if invoice with that transaction id already exists
				$invoiceObj = [];
                $invoiceSearch = Invoice::where('invoice_date', $invoice['invoice_date'])
                    ->where('amount', $invoice['amount']);
				if ($invoiceSearch->get()->count() == 0) {
					$invoice['user_id'] = Auth::user()->id;
					$invoiceObj = Invoice::create($invoice);
				} else {
					$invoiceObj = $invoiceSearch->first();
                    $invoiceObj->update($invoice);
				}
                $invoiceObj->uploaded = 1;
                $invoiceObj->save();

				// Store invoice file (if it is paid)
				if ($invoice['status'] == 'paid') {
					$file->storeAs($invoiceObj->getStoragePath(), $invoiceObj->getFilename());
				}

				array_push($invoices_uploaded, $invoice);
				$invoices_uploaded_count += 1;
			}
		}

		return view('invoice-upload')->with([
			'invoices_uploaded_success' => true,
			'invoices_uploaded' => $invoices_uploaded,
			'invoices_uploaded_count' => $invoices_uploaded_count,
			'error' => '',
		]);
    }

    public function invoicesCount($invoices) {
        $invoicesCount = array(
            'Tutti' => array(
                'count' => 0,
                'total_amount' => 0.00,
            ),
        );
        
        foreach ($invoices as $invoice) {
            $invoicesCount['Tutti']['count'] += 1;
            $invoicesCount['Tutti']['total_amount'] += floatval($invoice->amount);
        }
        
        return $invoicesCount;
    }

    public function filter(Request $request, $admin = false) {
        $fromDate = $request->input('filter_date_from', '');
        $toDate = $request->input('filter_date_to', '');

        $minAmount = '';
        $minAmountSet = false;
        if (is_numeric($request->input('filter_min_amount'))) {
        	$minAmountSet = true;
        	$minAmount = floatval($request->input('filter_min_amount'));
        }
        $maxAmount = '';
        $maxAmountSet = false;
        if (is_numeric($request->input('filter_max_amount'))) {
        	$maxAmountSet = true;
        	$maxAmount = floatval($request->input('filter_max_amount'));
        }

        $filter_uploaded = $request->input('filter_uploaded', '');
        $filter_transaction = $request->input('filter_transaction', '');
        $filter_status = $request->input('filter_status', '');
        $filter_auto_invoice = $request->input('filter_auto_invoice', 'all');

        $page_size = 500;
        if (is_numeric($request->input('page_size', 0)) && $request->input('page_size', 0) > 0) {
        	$page_size = $request->input('page_size', 0);
    	}

        $admin = $admin && $this->permissionGuard('admin_view');

        $invoices = [];

        if ($admin) {
        	$invoices = Invoice::query();
        } else {
        	$invoices = Invoice::where('user_id', Auth::user()->id);
        }

        if ($fromDate == '' && $toDate != '') {
        	$to = $toDate . ' 23:59:59';
            $invoices = $invoices->where('invoice_date', '<=', $to);
        } elseif ($fromDate != '' && $toDate == '') {
        	$from = $fromDate . ' 00:00:00';
            $invoices = $invoices->where('invoice_date', '>=', $from);
        } elseif ($fromDate != '' && $toDate != '') {
        	$from = $fromDate . ' 00:00:00';
        	$to = $toDate . ' 23:59:59';
            $invoices = $invoices->whereBetween('invoice_date', [$from, $to]);
        }

        if ($minAmountSet) {
        	$invoices = $invoices->where('amount', '>=', $minAmount);
        }
        if ($maxAmountSet) {
        	$invoices = $invoices->where('amount', '<=', $maxAmount);
        }

        if ($filter_status != '') {
            $invoices = $invoices->where('status', $filter_status);
        }

        if ($filter_auto_invoice == 'yes') {
            $invoices = $invoices->where('info->auto_invoice', true);
        } elseif ($filter_auto_invoice == 'no') {
            $invoices = $invoices->where(function ($q) {
                $q->whereNull('info->auto_invoice')->orWhere('info->auto_invoice', false);
            });
        }

        if ($filter_uploaded == 'yes') {
            $invoices = $invoices->where('uploaded', 1);
        } elseif ($filter_uploaded == 'no') {
            $invoices = $invoices->where('uploaded', 0);
        }
        if ($filter_transaction == 'yes') {
            $invoices = $invoices->where('transaction', 1);
        } elseif ($filter_transaction == 'no') {
            $invoices = $invoices->where('transaction', 0);
        }

        // Return view
        $invoices = $invoices->orderBy('invoice_date', 'desc');
        $invoicesObjArray = $invoices;
        $invoicesCount = $this->invoicesCount($invoices->get());
        $invoices = InvoiceResource::collection($invoices->paginate($page_size));

        return [
            'invoices_obj_array' => $invoicesObjArray->get(),
            'invoices_count' => $invoicesCount,
            'invoices' => $invoices,

            'admin' => $admin,

            'filter_date_from' => $fromDate,
            'filter_date_to' => $toDate,

            'filter_min_amount' => $minAmount,
            'filter_max_amount' => $maxAmount,

            'filter_uploaded' => $filter_uploaded,
            'filter_transaction' => $filter_transaction,
            'filter_status' => $filter_status,
            'filter_auto_invoice' => $filter_auto_invoice,

            'page_size' => $page_size,
        ];
    }

    public function index(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('view')) {
            return redirect()->route('dashboard');
        }

        // Redirect to admin/invoices if it has permission of admin_view
        if ($this->permissionGuard('admin_view')) {
            return redirect()->route('admin/invoices');
        }

        return view('invoices')->with($this->filter($request, false));
    }

    public function adminIndex(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('admin_view')) {
            return redirect()->route('invoices');
        }

        return view('invoices')->with($this->filter($request, true));
    }

    public function delete($id) {
        // Delete the invoice
        $invoice = Invoice::findOrFail($id);

        if ($invoice->user_id != Auth::user()->id &&
            !Auth::user()->hasPermission('admin') &&
            !Auth::user()->hasPermission('accountant')) {
            return array(
                "result" => false
            );
        }

        // Delete invoice file from storage
        Storage::delete($invoice->getStoragePathname());

        // Delete invoice record
        if ($invoice->delete())
        {
            return new InvoiceResource($invoice);
        }
    }

    public function download($id) {
    	$invoice = Invoice::findOrFail($id);

    	if ($invoice->user_id != Auth::user()->id &&
            !Auth::user()->hasPermission('admin') &&
            !Auth::user()->hasPermission('accountant')) {
            return array(
                "result" => false
            );
        }

		if(!Storage::exists($invoice->getStoragePathname())) {
			return array(
                "result" => false
            );
		}

    	return Storage::download($invoice->getStoragePathname());
    }

    public function downloadMultipleInvoices(Request $request) {
    	$id_array = json_decode($request->input('id_array', []));

    	if (!is_array($id_array) || count($id_array) == 0) {
            return array(
                "result" => false,
                "message" => "Missing parameter id_array",
            );
        }

		$zip_file = storage_path('app/public/invoices.zip');

		// Initializing PHP class
		$zip = new \ZipArchive();
		if ($zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
			return array(
                "result" => false,
                "message" => "Unexpected error: cannot create zip file",
            );
		}

        $invoice_added = 0;
		foreach ($id_array as $id) {
			$invoice = Invoice::findOrFail($id);

	    	if ($invoice->user_id != Auth::user()->id && !Auth::user()->hasPermission('admin')) {
	            return array(
	                "result" => false
	            );
	        }

	        if(!Storage::exists($invoice->getStoragePathname())) {
	        	continue;
	        }

	    	$invoice_file = $invoice->original_file_name;
	    	$file_pathname = storage_path('app/' . $invoice->getStoragePathname());
	    	if ($zip->addFile($file_pathname, $invoice_file) !== TRUE) {
	    		return array(
	                "result" => false,
	                "message" => "File '" . $file_pathname . "' does not exist",
	            );
	    	}
            $invoice_added += 1;
		}

        if ($invoice_added == 0) {
            return array(
                "result" => false,
                "message" => "There are no files for the invoices you trying to upload",
            );
        }

		$zip->close();

		return response()->download($zip_file);
    }

    public function setAutoInvoice($id, Request $request) {
        if (!Auth::user()->hasPermission('admin')) {
            return array(
                "result" => false,
                "message" => "You do not have permission"
            );
        }

        $val = $request->input('auto_invoice_value', true);
        if ($val) {
            $val = true;
        } else {
            $val = false;
        }

        $invoice = Invoice::findOrFail($id);
        $invoice->setAutoInvoice($val);

        return array(
            "result" => true
        );
    }

    public function uploadTransactionsIndex() {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('upload_transactions')) {
            return redirect()->route('dashboard');
        }

        return view('transactions-upload');
    }

    public function uploadTransactions(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('upload_transactions')) {
            return redirect()->route('dashboard');
        }

        $transactions_uploaded_count = 0;
        if($request->hasFile('file')) {
            $file = $request->file('file');

            $handle = fopen($file->getPathname(), "r");

            if ($file->getClientOriginalExtension() != 'csv') {
                return view('transactions-upload')->with([
                    'transactions_uploaded_success' => true,
                    'error' => 'You must upload only .csv files',
                ]);
            }

            $header = true;
            while ($csvLine = fgetcsv($handle, 1000, ",")) {
                if ($header) {
                    $header = false;
                } else {
                    if ($csvLine[$this->TRANSACTION_DESCRIPTION_COL_NUMBER] != 'Facebook') {
                        continue;
                    }

                    $transactions_uploaded_count += 1;

                    $invoice_date = date("Y-m-d h:i:s", strtotime($csvLine[$this->TRANSACTION_DATE_COL_NUMBER]));
                    $invoice_amount = floatval(str_replace('-', '', str_replace('€', '', $csvLine[$this->TRANSACTION_AMOUNT_COL_NUMBER])));
                    $invoice_currency = 'eur';
                    if (strtolower($csvLine[$this->TRANSACTION_CURRENCY_COL_NUMBER]) == 'usd') {
                        $invoice_currency = 'usd';
                    }

                    $invoice = Invoice::where('invoice_date', $invoice_date)->where('amount', $invoice_amount);
                    if ($invoice->get()->count() > 0) {
                        $invoice = $invoice->first();

                        $invoice->transaction = 1;
                        $invoice->save();
                    } else {
                        $invoice = [
                            'user_id' => Auth::user()->id,
                            'uploaded' => 0,
                            'transaction' => 1,
                            'invoice_date' => $invoice_date,
                            'amount' => $invoice_amount,
                            'currency' => $invoice_currency,
                            'status' => 'paid',
                        ];
                        Invoice::create($invoice);
                    }
                }
            }
            fclose($handle);
        } else {
            return view('transactions-upload')->with([
                'transactions_uploaded_success' => true,
                'error' => 'No files uploaded',
            ]);
        }

        return view('transactions-upload')->with([
            'transactions_uploaded_success' => true,
            'transactions_uploaded_count' => $transactions_uploaded_count,
            'error' => '',
        ]);
    }

    private function summaryDownload(Request $request, $file_name_download) {
        $invoices = $this->filter($request, true);
        $invoices = $invoices['invoices_obj_array'];

        $file_name = storage_path('app/public/' . $file_name_download);
        $handle = fopen($file_name, "w");

        $header = array('data', 'numero fattaura', 'importo', 'metodo pagamento');
        fputcsv($handle, $header);

        foreach ($invoices as $invoice) {
            $method = ($invoice->status == 'paid') ? 'SOLDO' : '';
            $data = array(
                $invoice->invoice_date,
                $invoice->number,
                $invoice->amount,
                $method,
            );
            fputcsv($handle, $data);
        }

        fclose($handle);

        return Storage::download('public/' . $file_name_download);
    }

    public function invocesSummaryDownoad(Request $request) {
        return $this->summaryDownload($request, 'invoices_summary.csv');
    }

    public function autoInvocesSummaryDownoad(Request $request) {
        return $this->summaryDownload($request, 'auto_invoices_summary.csv');
    }
}
