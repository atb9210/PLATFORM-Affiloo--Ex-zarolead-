<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'info',
    ];

    protected $casts = [
        'info' => 'array'
    ];

    public function forms()
    {
        return $this->hasMany('App\Form');
    }

    public function products()
    {
        return $this->hasMany('App\Product');
    }

    public function landings()
    {
        return $this->hasMany('App\Landing');
    }
}
