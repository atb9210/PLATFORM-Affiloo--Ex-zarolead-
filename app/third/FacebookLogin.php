<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class FacebookLogin extends Model
{
    protected $table = 'third_facebook_logins';

    protected $fillable = [
        'access_token', 'type',
    ];

    public static function getInstance()
    {
        $fbLogin = FacebookLogin::first();
        if ($fbLogin === null) {
            $fbLogin = FacebookLogin::create();
        }
        return $fbLogin;
    }

    public static function getAccessToken()
    {
        $fbLogin = FacebookLogin::getInstance();
        return $fbLogin->access_token;
    }

    public static function setAccessToken(string $accessToken = null)
    {
        $fbLogin = FacebookLogin::getInstance();
        $fbLogin->access_token = $accessToken;
        $fbLogin->save();
    }
}
