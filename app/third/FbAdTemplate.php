<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class FbAdTemplate extends Model
{
    protected $table = 'third_fb_ad_templates';

    protected $fillable = [
        'name', 'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];
}
