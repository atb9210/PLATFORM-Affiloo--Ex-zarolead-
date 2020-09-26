<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'uploaded', 'transaction',
        'language', 'invoice_date', 'transaction_id', 'number', 'status', 'amount', 'currency', 'type',
        'info', 'original_file_name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $casts = [
        'info' => 'array'
    ];

    /**
     * Invoice constants
     */

    private const TRANSACTION_ID_KEYWORDS = [
        'eng' => 'Transaction ID',
        'it' => 'ID Transazione',
    ];

    private const NUMBER_KEYWORDS = [
        'eng' => 'Invoice # ',
        'it' => 'Num. fattura ',
    ];

    private const MONTHS = [
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

    private const STATUSES = [
        'eng' => [
            'Facebook Paid' => 'paid',
            'Facebook Failed' => 'failed',
        ],
        'it' => [
            'Facebook Pagamento effettuato' => 'paid',
            'Facebook Operazione non riuscita' => 'failed',
        ],
    ];

    private const CURRENCIES = [
        'eur' => '€',
        'usd' => '$',
    ];

    private const TRANSACTION_DATE_COL_NUMBER = 1;
    private const TRANSACTION_DESCRIPTION_COL_NUMBER = 8;
    private const TRANSACTION_AMOUNT_COL_NUMBER = 9;
    private const TRANSACTION_CURRENCY_COL_NUMBER = 10;

    public function user()
    {
		return $this->belongsTo('App\User', 'user_id');
	}

    public function getStoragePath()
    {
        $pathname = env('DROPBOX_FOLDER') . '/invoices/';
        $formatted_date = \Carbon\Carbon::parse($this->invoice_date)->format('Y-m-d');
        $tmp = explode('-', $formatted_date);
        $pathname .= $tmp[0] . '/' . $tmp[1];
        return $pathname;
    }

    public function getFilename()
    {
        return $this->transaction_id . '.pdf';
    }

    public function getStoragePathname()
    {
        return $this->getStoragePath() . '/' . $this->getFilename();
    }

    private static function fetchRawData(UploadedFile $file)
    {
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

        // Get file text
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($file->getPathname());
        $text = $pdf->getText();
        $rows = explode("\n", $text);

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
            if (strpos($rows[$i], self::TRANSACTION_ID_KEYWORDS[$lang]) !== false) {
                $transaction_id_row_number = $i + 1;
                continue;
            }

            if (strpos($rows[$i], self::NUMBER_KEYWORDS[$lang]) !== false) {
                $number_row_number = $i;
                continue;
            }

            foreach (self::STATUSES[$lang] as $key => $value) {
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

    private static function processRawData(array $rawData)
    {
        $invoiceData = array(
    		'language' => '',
    		'invoice_date' => '',
    		'transaction_id' => '',
            'number' => '',
    		'status' => '',
    		'amount' => '',
            'currency' => '',
    	);

    	$invoiceData['language'] = $rawData['language'];

    	if ($rawData['language'] == 'eng') {
    		// Process data
    		$words = explode(' ', str_replace(',', '', $rawData['invoice_date']));
    		$d = $words[1];
    		$m = self::MONTHS[$rawData['language']][$words[0]];
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

    		$invoiceData['invoice_date'] = $y . '/' . $m . '/' . $d . ' ' . $h . ':' . $min . ':00';
    	} else if ($rawData['language'] == 'it') {
    		// Process data
    		$words = explode(' ', str_replace(',', '', $rawData['invoice_date']));
    		$d = $words[0];
    		$m = self::MONTHS[$rawData['language']][$words[1]];
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

    		$invoiceData['invoice_date'] = $y . '/' . $m . '/' . $d . ' ' . $h . ':' . $min . ':00';

            $rawData['amount'] = str_replace(',', '.', str_replace('.', '', $rawData['amount']));
    	}

    	$words = explode(' ', $rawData['transaction_id']);
    	$invoiceData['transaction_id'] = $words[count($words) - 1];

        if ($rawData['number'] != '') {
            $invoiceData['number'] = explode(self::NUMBER_KEYWORDS[$invoiceData['language']], $rawData['number'])[1];
        }

        $currency = '';
        foreach (self::CURRENCIES as $key => $value) {
            if (strpos($rawData['amount'], $value) !== false) {
                $currency = $key;
                break;
            }
        }
        if ($currency == '') {
            abort(401, "Invalid currency in invoice");
        }
        $invoiceData['currency'] = $currency;

        $invoiceData['status'] = self::STATUSES[$rawData['language']][$rawData['status']];

        $rawAmount = str_replace(self::CURRENCIES[$currency], '', $rawData['amount']);
        $invoiceData['amount'] = floatval($rawAmount);

    	return $invoiceData;
    }

    public static function createFromFile(UploadedFile $file)
    {
        $invoiceParams = self::processRawData(self::fetchRawData($file));
        $invoiceParams['original_file_name'] = $file->getClientOriginalName();

        $invoice = null;
        $invoiceQuery = Invoice::where('invoice_date', $invoiceParams['invoice_date'])
            ->where('amount', $invoiceParams['amount'])
            ->where('archived', 0);
        if ($invoiceQuery->get()->count() == 0) {
            $invoiceParams['user_id'] = Auth::user()->id;
            $invoice = Invoice::create($invoiceParams);
        } else {
            $invoice = $invoiceQuery->first();
            $invoice->update($invoiceParams);
        }

        if ($invoice->status == 'paid') {
            $path = Storage::disk('dropbox')->putFileAs($invoice->getStoragePath(), $file, $invoice->getFilename());
            if ($path != null) {
                $invoice->pathname = $path;
                $invoice->uploaded = 1;
                $invoice->save();
            }
        }

        return $invoice;
    }
}
