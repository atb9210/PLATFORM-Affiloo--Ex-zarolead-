<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_id',
    ];

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function forms() {
        return $this->hasMany('App\Form');
    }

    public function landings() {
        return $this->hasMany('App\Landing');
    }
}
