<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class DropinProduct extends Model
{
    protected $table = 'third_dropin_products';
    protected $primaryKey = 'sku';

    protected $fillable = [
        'sku', 'name', 'variations', 'description', 'category', 'images',
        'dropshipping_price', 'sale_price', 'cost', 'quantity',
    ];

    protected $casts = [
        'sku' => 'string',
        'images' => 'array',
        'dropshipping_price' => 'float',
        'sale_price' => 'float',
        'cost' => 'float',
        'quantity' => 'integer',
    ];
}
