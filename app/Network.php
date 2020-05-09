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

    public function orders() {
        return $this->hasMany('App\Order');
    }
}
