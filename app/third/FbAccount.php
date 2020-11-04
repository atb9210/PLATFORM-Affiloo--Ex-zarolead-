<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class FbAccount extends Model
{
    protected $table = '3rd_fb_accounts';

    protected $fillable = [
        'user_id', 'gologin_id', 'dolphin_id', 'profile', 'name', 'product_id',
        'notes', 'cards', 'token', 'info', 'status', 'token_status',
    ];

    protected $casts = [
        'profile' => 'array',
        'info' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
