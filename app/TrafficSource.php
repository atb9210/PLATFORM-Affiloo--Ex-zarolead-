<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrafficSource extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'custom_params',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $casts = [
        'custom_params' => 'array'
    ];

    public function forms()
    {
        return $this->hasMany('App\Form');
    }
}
