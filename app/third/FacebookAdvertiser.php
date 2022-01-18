<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FacebookAdvertiser extends Model
{
    protected $table = 'third_facebook_advertisers';

    protected $fillable = [
        'name', 'access_token',
    ];
}
