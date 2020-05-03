<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model {
    const WAITING = 1;
    const CONFIRMED = 2;
    const NOT_RESPONDING = 3;
    const REFUSED = 4;
    const NOT_SENT = 5;
    const DOUBLE = 6;

    const ENUM = [
        [
            "id" => self::WAITING,
            "description" => "waiting",
        ],
        [
            "id" => self::CONFIRMED,
            "description" => "confirmed",
        ],
        [
            "id" => self::NOT_RESPONDING,
            "description" => "not responding",
        ],
        [
            "id" => self::REFUSED,
            "description" => "refused",
        ],
        [
            "id" => self::NOT_SENT,
            "description" => "not sent",
        ],
        [
            "id" => self::DOUBLE,
            "description" => "double",
        ],
    ];

    protected $fillable = [
        'description',
    ];

    public function orders() {
        return $this->hasMany('App\Order');
    }
}
