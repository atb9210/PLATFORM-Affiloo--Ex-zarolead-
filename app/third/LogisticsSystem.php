<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class LogisticsSystem extends Model
{
    protected $table = 'third_logistics_systems';

    protected $fillable = [
        'name', 'api', 'settings',
    ];

    protected $casts = [
        'api' => 'array',
        'settings' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany('App\third\LogisticsOrder');
    }
}
