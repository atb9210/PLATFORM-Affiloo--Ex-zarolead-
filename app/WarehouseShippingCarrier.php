<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseShippingCarrier extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'plan',
    ];

    protected $casts = [
        'plan' => 'array',
    ];

    public function shippings()
    {
        return $this->hasMany('App\WarehouseShipping');
    }
}
