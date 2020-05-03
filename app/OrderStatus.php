<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
  protected $fillable = [
    'description',
  ];

  public function orders()
  {
    return $this->hasMany('App\Order');
  }
}
