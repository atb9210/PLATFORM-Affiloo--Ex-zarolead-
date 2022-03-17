<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseShipping extends Model
{
    const STATUSES = [
        'unprocessed',
        'not_shipped',
        'waiting_for_carrier',
        'assigned_to_carrier',
        'shipped',
        'on_the_way',
        'delivered',
        'returned',
        'stock',
        'released',
        'lost',
        'destroyed',
        'invalid',
        'out_of_stock',
    ];

    protected $fillable = [
        'warehouse_order_id',
        'warehouse_shipping_carrier_id',
        'status',
        'tracking_number', 'tracking_url',
        'created_at', 'updated_at', 'delivered_at',
        'address_line1', 'address_line2', 'city', 'postcode', 'zone', 'country',
        'notes',
        'info',
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo('App\WarehouseOrder', 'warehouse_order_id');
    }

    public function carrier()
    {
        return $this->belongsTo('App\WarehouseShippingCarrier', 'warehouse_shipping_carrier_id');
    }
}
