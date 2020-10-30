<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'traffic_source_id', 'product_id',
        'ctr', 'cpc', 'cpa',
        'info', 'product_media_id', 'product_media_preview_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function traffic_source()
    {
        return $this->belongsTo('App\TrafficSource', 'traffic_source_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function product_media()
    {
        return $this->belongsTo('App\ProductMedia', 'product_media_id');
    }

    public function product_media_preview()
    {
        return $this->belongsTo('App\ProductMedia', 'product_media_preview_id');
    }
}
