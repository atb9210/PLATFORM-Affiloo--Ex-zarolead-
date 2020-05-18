<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Network extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'api' => 'array'
    ];

    public function orders() {
        return $this->hasMany('App\Order');
    }

    public function forms() {
        return $this->hasMany('App\Form');
    }
}
