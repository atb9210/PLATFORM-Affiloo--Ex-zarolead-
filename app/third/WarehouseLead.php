<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class WarehouseLead extends Model
{
    protected $table = 'third_warehouse_leads';

    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email', 'status', 'info', 'notes',
    ];

    protected $casts = [
        'info' => 'array',
    ];
}
