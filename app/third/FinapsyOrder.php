<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class FinapsyOrder extends Model
{
    protected $table = 'third_finapsy_orders';

    protected $fillable = [
        'order_id', 'info', 'status',
    ];

    protected $casts = [
        'info' => 'array',
    ];
}
