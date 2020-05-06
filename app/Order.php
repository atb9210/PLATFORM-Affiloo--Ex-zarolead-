<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'user_id',
        'order_status_id',
        'product_id',
    ];
    
    public function status() {
        return $this->belongsTo('App\OrderStatus', 'order_status_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
