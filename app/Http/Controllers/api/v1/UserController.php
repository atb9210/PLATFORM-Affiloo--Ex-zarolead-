<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;

class UserController extends Controller {
    /**
     * Get all users list (with informations).
     *
     * @response {
     *  "users" => [users],
     * }
     */
    public function index() {
        return response([
            'users' => User::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Get user's informations.
     *
     * @urlParam id int required User's id
     *
     * @response {
     *  "user" => [user],
     * }
     *
     * @response 404 {
     *  "message" => "User not found!",
     * }
     */
    public function show($id) {
        $user = User::find($id);

        if ($user) {
            return response([
                "user" => $user;
            ]);
        } else {
            return response([
                "message" => "User not found!",
            ], 404);
        }
    }

    /**
     * Update the user's field.
     *
     * @urlParam id int required User's id
     *
     * @bodyParam name string User's new name
     * @bodyParam email string User's new email
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }
}
