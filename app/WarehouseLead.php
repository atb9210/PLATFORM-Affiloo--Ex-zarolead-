<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseLead extends Model
{
    const STATUSES = [
        'to_recall',
        'unprocessed',
        'no_asnwer',
        'fake',
        'cancelled',
        'duplicate',
        'waiting_payment',
        'confirmed',
    ];

    protected $fillable = [
        'customer_id',
        'status',
        'first_name', 'last_name', 'phone', 'email',
        'address_line1', 'address_line2', 'city', 'postcode', 'zone', 'country',
        'info', 'archived',
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }
}
