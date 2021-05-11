<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class FbCampaign extends Model
{
    protected $table = 'third_fb_campaigns';

    protected $fillable = [
        'id', 'form_id',
    ];

    public function form()
    {
        return $this->belongsTo('App\Form', 'form_id');
    }
}
