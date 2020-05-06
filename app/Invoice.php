<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'uploaded', 'transaction',
        'language', 'invoice_date', 'transaction_id', 'number', 'status', 'amount', 'currency',
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

    public function user() {
		return $this->belongsTo('App\User', 'user_id');
	}

    public function setAutoInvoice($value = true) {
        $info = $this->info;
        $info['auto_invoice'] = $value;
        $this->info = $info;
        $this->save();
    }

    public function getStoragePath() {
        $pathname = 'public/invoices/';
        $formatted_date = \Carbon\Carbon::parse($this->invoice_date)->format('Y-m-d');
        $tmp = explode('-', $formatted_date);
        $pathname .= $tmp[0] . '/' . $tmp[1];
        return $pathname;
    }

    public function getFilename() {
        return $this->transaction_id . '.pdf';
    }

    public function getStoragePathname() {
        return $this->getStoragePath() . '/' . $this->getFilename();
    }
}
