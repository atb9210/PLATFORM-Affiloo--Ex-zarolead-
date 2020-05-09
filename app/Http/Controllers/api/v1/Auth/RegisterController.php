<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\User;

class RegisterController extends Controller
{
    /**
     * Register a new user.
     *
     * @bodyParam name string required User's name
     * @bodyParam email string required User's email
     * @bodyParam password string required User's password
     * @bodyParam password_confirmation string required Repeat user's password
     *
     * @response 201 {
     *  "user": user
     * }
     */
    public function register(Request $request) {
        // Param validation
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|min:4|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response([
            "user" => $user,
        ], 201);
    }
}
