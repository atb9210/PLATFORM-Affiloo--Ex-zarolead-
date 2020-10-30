<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\third\FbAccount as FbAccount;
use App\Http\Resources\api\third\FbAccountResource;

class FbAccountController extends Controller
{
    /**
     * Get all fb accounts list.
     *
     * @response {
     *  "data": [fb_accounts],
     * }
     */
    public function index()
    {
        //$this->authorize('viewAny', 'App\Tag');
        return FbAccountResource::collection(FbAccount::query()->get());
    }

    /**
     * Get fb account's informations.
     *
     * @urlParam id int required Fb account's id
     *
     * @response {
     *  "data": fb_account,
     * }
     */
    public function show($id)
    {
        $fb_account = FbAccount::findOrFail($id);
        //$this->authorize('view', $tag);

        return new FbAccountResource($fb_account);
    }

    /**
     * Create a new fb account.
     *
     * @bodyParam user_id int required Fb account's owner
     * @bodyParam name string required Fb account's name
     * @bodyParam notes string Fb account's notes
     * @bodyParam token string required Fb account's token
     * @bodyParam status string Fb account's status
     *
     * @response 201 {
     *  "message": "Fb account created",
     *  "data": fb_account,
     * }
     */
    public function store(Request $request)
    {
        //$this->authorize('create', 'App\Tag');

        // Params validation
        $params = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|min:1',
            'notes' => 'string|nullable',
            'token' => 'required|string|min:3',
            'status' => 'in:active,disabled',
        ]);

        $fb_account = FbAccount::create($params);

        return response([
            "message" => "Fb account created",
            "data" => new FbAccountResource($fb_account),
        ], 201);
    }

    /**
     * Update the fb account's fields.
     *
     * @urlParam id int required Fb account's id
     *
     * @bodyParam user_id int Fb account's owner
     * @bodyParam name string Fb account's name
     * @bodyParam notes string Fb account's notes
     * @bodyParam token string Fb account's token
     * @bodyParam status string Fb account's status
     *
     * @response {
     *  "data": fb_account,
     * }
     */
    public function update(Request $request, $id)
    {
        $fb_account = FbAccount::findOrFail($id);
        //$this->authorize('update', $tag);

        // Params validation
        $params = $request->validate([
            'user_id' => 'integer|exists:users,id',
            'name' => 'string|min:1',
            'notes' => 'string|nullable',
            'token' => 'string|min:3',
            'status' => 'in:active,disabled',
        ]);

        $fb_account->update($params);

        return new FbAccountResource($fb_account);
    }

    /**
     * Delete the specified fb account.
     *
     * @urlParam id int required fb account's id
     *
     * @response {
     *  "message": "Fb account has been delete",
     * }
     */
    public function destroy($id)
    {
        $fb_account = FbAccount::findOrFail($id);

        //$this->authorize('delete', $tag);

        $fb_account->delete();

        return response([
            'message' => 'Fb account has been deleted'
        ]);
    }
}
