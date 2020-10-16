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
        'name', 'custom_params', 'ad_model',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $casts = [
        'custom_params' => 'array',
        'ad_model' => 'array'
    ];

    public function forms()
    {
        return $this->hasMany('App\Form');
    }

    public function flows()
    {
        return $this->hasMany('App\Flows');
    }

    public function getAdModel($key)
    {
        $keys = explode('.', $key);
        $ad_model = $this->ad_model;
        for ($i = 0; $i < count($keys); $i++) {
            if (!isset($ad_model[$keys[$i]])) {
                return [];
            }
            $ad_model = $ad_model[$keys[$i]];
        }
        return $ad_model;
    }
}
