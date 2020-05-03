<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Landing extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'product_id', 'tag_id', 'url', 'country'
    ];

	public function product()
	{
		return $this->belongsTo('App\Product', 'product_id');
	}

    public function tag()
	{
		return $this->belongsTo('App\Tag', 'tag_id');
	}
}
