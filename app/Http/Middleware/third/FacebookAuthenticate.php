<?php

namespace App\Http\Middleware\third;

use Closure;

use Illuminate\Support\Facades\Auth;

use App\third\FacebookLogin;

class FacebookAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // TODO: Check if token is valid
        $fbLogin = FacebookLogin::getCurrent();
        $accessToken = $request->session()->get('facebook_access_token');
        $dateTimeNow = new \DateTime();
        if ($fbLogin === null ||
            $fbLogin->access_token === null || $fbLogin->access_token === '' ||
            $fbLogin->expires_at === null || $fbLogin->expires_at < $dateTimeNow) {
            return response([
                'message' => 'You must be logged in your facebook account',
            ], 401);
        }
        return $next($request);
    }
}
