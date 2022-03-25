<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseProduct extends Model
{
    protected $fillable = [
        'name', 'sku',
        'info',
    ];

    public function variations()
    {
        return $this->hasMany('App\WarehouseProductVariation');
    }

    public static function findBySku(string $sku): WarehouseProduct|null
    {
        return self::where('sku', $sku)->first();
    }
}
