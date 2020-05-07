<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller {
    /**
     * Create and get an access token by logging in with email and password.
     *
     * @bodyParam email string required User's email
     * @bodyParam password string required User's password
     *
     * @response {
     *  "user" => [user],
     *  "access_token" => [access_token],
     * }
     */
    public function login(Request $request) {
        // Param validation
        $data = $request->validate([
            'email' => 'required|email|min:4|max:255',
            'password' => 'required|string',
        ]);
        
        // Login
        if (!Auth::attempt($data)) {
            return response([
                'message' => 'Invalid login credentials!'
            ], 401);
        }

        // Generate access token
        $accessToken = Auth::user()->createToken('authToken')->accessToken;
        return response([
            'user' => Auth::user(),
            'access_token' => $accessToken,
        ]);
    }
}
