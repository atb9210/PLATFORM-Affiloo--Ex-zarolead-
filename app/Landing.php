<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Landing extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'campaign_id', 'form_id', 'tag_id', 'url', 'country'
    ];

    public function campaign() {
        return $this->belongsTo('App\Campaign', 'campaign_id');
    }

	public function form() {
		return $this->belongsTo('App\Form', 'form_id');
	}

    public function tag() {
		return $this->belongsTo('App\Tag', 'tag_id');
	}
}
