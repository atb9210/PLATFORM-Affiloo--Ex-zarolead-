<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\User as User;

class UserController extends Controller {
    /**
     * Get all users list (with informations).
     *
     * @response {
     *  "users" => [users],
     * }
     */
    public function index() {
        $this->authorize('viewAny', 'App\User');

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
        $user = User::findOrFail($id);
        $this->authorize('view', $user);

        return response([
            "user" => $user,
        ]);
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
        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        // Param validation
        $data = $request->validate([
            'name' => 'string|min:3|max:255',
            'email' => 'email|min:4|max:255',
        ]);

        $user->update($data);

        return response([
            "user" => $user,
        ]);    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $user = User::findOrFail($id);

        $this->authorize('delete', $user);

        // Delete the user
        $user->delete();

        return response([
            "message" => "User has been deleted"
        ]);
    }
}
