<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FacebookCopy extends Model
{
    protected $table = 'third_facebook_copys';

    protected $fillable = [
        'ad_title', 'ad_text', 'product_id',
    ];
}
