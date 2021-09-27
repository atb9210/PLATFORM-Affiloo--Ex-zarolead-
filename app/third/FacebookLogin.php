<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;

class FacebookLogin extends Model
{
    protected $table = 'third_facebook_logins';

    protected $fillable = [
        'user_id', 'access_token', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public static function getCurrent()
    {
        if (Auth::user() === null) {
            return null;
        }
        return FacebookLogin::where('user_id', Auth::user()->id)->first();
    }
}
