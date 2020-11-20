<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class LogisticsOrder extends Model
{
    protected $table = 'third_logistics_orders';

    protected $fillable = [
        'logistics_system_id', 'order_id', 'info', 'status',
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public function system()
    {
        return $this->belongsTo('App\third\LogisticsSystem', 'logistics_system_id');
    }
}
