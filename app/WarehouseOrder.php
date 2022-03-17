<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseOrder extends Model
{
    const STATUSES = [
        'waiting_payment',
        'out_of_stock',
        'failed',
        'cancelled',
        'refunded',
        'confirmed',
    ];

    protected $fillable = [
        'customer_id',
        'amount',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }

    public function lines()
    {
        return $this->hasMany('App\WarehouseOrderLine');
    }
}
