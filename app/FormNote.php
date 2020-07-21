<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormNote extends Model
{
    protected $fillable = [
        'user_id', 'form_id', 'body',
    ];

	public function form() {
		return $this->belongsTo('App\Form', 'form_id');
	}
}
