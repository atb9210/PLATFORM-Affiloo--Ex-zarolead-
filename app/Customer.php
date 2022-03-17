<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email',
        'address_line1', 'address_line2', 'city', 'postcode', 'zone', 'country',
        'info', 'archived',
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany('App\WarehouseOrder');
    }
}
