<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    protected $table = 'product_medias';

    protected $fillable = [
        'user_id',
        'product_id',
        'type', 'pathname', 'original_file_name', 'public_url',
    ];

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function ads()
    {
        return $this->hasMany('App\Ad');
    }

    public function getStoragePath() : string
    {
        $pathname = 'public/product_medias/product_' . $this->product_id;
        return $pathname;
    }
}
