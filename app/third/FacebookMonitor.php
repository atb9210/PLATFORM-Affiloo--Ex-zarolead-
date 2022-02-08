<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FacebookMonitor extends Model
{
    protected $table = 'third_facebook_monitors';

    protected $fillable = [
        'name', 'advertiser_id', 'form_id', 'campaigns',
    ];

    protected $casts = [
        'campaigns' => 'array',
    ];
}
