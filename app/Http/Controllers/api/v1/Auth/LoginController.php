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
     *
     * @response 401 {
     *  "message" => "Invalid login credentials!",
     * }
     *
     * @response 400 {
     *  "errors" => [errors],
     * }
     */
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response([
                "errors" => $validator->errors(),
            ], 400);
        }
        
        $credentials = [
            'email' => $validator->valid()['email'],
            'password' => $validator->valid()['password'],
        ];

        if (!Auth::attempt($credentials)) {
            return response([
                'message' => 'Invalid login credentials!'
            ], 401);
        }

        $accessToken = Auth::user()->createToken('authToken')->accessToken;
        return response([
            'user' => Auth::user(),
            'access_token' => $accessToken,
        ]);
    }
}
