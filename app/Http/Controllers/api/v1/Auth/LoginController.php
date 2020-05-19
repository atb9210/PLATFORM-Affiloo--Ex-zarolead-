<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Client;

use App\User;

class LoginController extends Controller
{
    /**
     * Create and get an access token by logging in with email and password.
     *
     * @bodyParam email string required User's email
     * @bodyParam password string required User's password
     *
     * @response {
     *  "user" => user,
     *  "token" => {
     *      "token_type" => "Bearer",
     *      "expires_in" => expires_in_seconds,
     *      "access_token" => access_token,
     *      "refresh_token" => refresh_token,
     * }
     */
    public function login(Request $request)
    {
        // Param validation
        $params = $request->validate([
            'email' => 'required|email|min:4|max:255',
            'password' => 'required|string',
        ]);

        // Check if the user is archived
        if (User::where('email', $params['email'])->where('archived', 1)->get()->count()) {
            return response([
                'message' => 'User archived!'
            ], 404);
        }

        $passwordGrantClient = Client::where('password_client', 1)->first();
        $data = [
            'grant_type' => 'password',
            'client_id' => $passwordGrantClient->id,
            'client_secret' => $passwordGrantClient->secret,
            'username' => $params['email'],
            'password' => $params['password'],
            'scope' => '*',
        ];
        $tokenRequest = Request::create(route('passport.token'), 'post', $data);

        $result = app()->handle($tokenRequest);
        
        // Login
        if (!Auth::attempt($params)) {
            return response([
                'message' => 'Invalid login credentials!'
            ], 401);
        }

        // Generate access token
        return response([
            'user' => Auth::user(),
            'token' => json_decode($result->getContent()),
        ]);
    }

    /**
     * Refresh access token.
     *
     * @response {
     *  "access_token" => [access_token],
     * }
     */
    public function refreshToken(Request $request)
    {
        // TODO: Really refresh the token
        // Return access token
        return response([
            'user' => Auth::user(),
            'access_token' => $request->input('access_token'),
        ]);
    }
}
