<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class EcommerceLead extends Model
{
    protected $table = 'third_ecommerce_leads';

    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email', 'status', 'info', 'notes',
    ];

    protected $casts = [
        'info' => 'array',
    ];
}
