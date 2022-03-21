<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseProductVariation extends Model
{
    protected $fillable = [
        'warehouse_product_id',
        'sku', 'name',
        'price', 'quantity', 'attributes',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo('App\WarehouseProduct', 'warehouse_product_id');
    }

    public static function findBySku(string $sku): WarehouseProductVariation|null
    {
        return self::where('sku', $sku)->first();
    }
}
