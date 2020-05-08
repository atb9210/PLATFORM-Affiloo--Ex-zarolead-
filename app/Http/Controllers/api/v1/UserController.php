<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User as User;
use App\Http\Resources\api\v1\UserResource;

class UserController extends Controller {
    /**
     * Get all users list.
     *
     * @response {
     *  "data": [users],
     * }
     */
    public function index() {
        $this->authorize('viewAny', 'App\User');

        return UserResource::collection(User::all());
    }

    /**
     * Get user's informations.
     *
     * @urlParam id int required User's id
     *
     * @response {
     *  "data": user,
     * }
     */
    public function show($id) {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    /**
     * Update the user's fields.
     *
     * @urlParam id int required User's id
     *
     * @bodyParam name string User's new name
     * @bodyParam email string User's new email
     *
     * @response {
     *  "data": user,
     * }
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

        return UserResource($user);
    }

    /**
     * Delete the specified user.
     *
     * @urlParam id int required User's id
     *
     * @response {
     *  "message": "User has been deleted",
     * }
     */
    public function destroy($id) {
        $user = User::findOrFail($id);

        $this->authorize('delete', $user);

        // Delete the user
        $user->delete();

        return response([
            "message" => "Resource has been deleted"
        ]);
    }
}
