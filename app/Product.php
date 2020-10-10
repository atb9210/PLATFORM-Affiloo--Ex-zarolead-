<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'tag_id', 'advertiser_id', 'country',
    ];

	public function advertiser()
    {
		return $this->belongsTo('App\User', 'advertiser_id');
	}

	public function tag()
    {
		return $this->belongsTo('App\Tag', 'tag_id');
	}

	public function forms()
    {
        return $this->hasMany('App\Form');
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
