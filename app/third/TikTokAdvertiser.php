<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TikTokAdvertiser extends Model
{
    protected $table = 'third_tiktok_advertisers';

    protected $fillable = [
        'id', 'name',
    ];

    public $incrementing = false;
}
