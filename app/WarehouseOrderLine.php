<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseOrderLine extends Model
{
    protected $fillable = [
        'warehouse_order_id',
        'warehouse_product_variation_id',
        'quantity', 'unit_price', 'total_price', 'discount', 'final_price',
        'attributes',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo('App\WarehouseOrder', 'warehouse_order_id');
    }

    public function product_variation()
    {
        return $this->belongsTo('App\WarehouseProductVariation', 'warehouse_product_variation_id');
    }
}
