<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class FbAccount extends Model
{
    protected $table = '3rd_fb_accounts';

    protected $fillable = [
        'user_id', 'gologin_id', 'profile', 'name', 'notes', 'token', 'status',
    ];

    protected $casts = [
        'profile' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
