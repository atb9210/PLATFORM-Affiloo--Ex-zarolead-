<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class FbAdRule extends Model
{
    protected $table = 'third_fb_ad_rules';

    protected $fillable = [
        'name', 'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];
}
