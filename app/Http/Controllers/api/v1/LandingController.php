<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Landing as Landing;
use App\Http\Resources\api\v1\LandingResource;

class LandingController extends Controller
{
    /**
     * Get all landings list.
     *
     * @response {
     *  "data": [landings],
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', 'App\Landing');

        return LandingResource::collection(Landing::where('archived', 0)->get());
    }

    /**
     * Get landing's informations.
     *
     * @urlParam id int required Landing's id
     *
     * @response {
     *  "data": landing,
     * }
     */
    public function show($id)
    {
        $landing = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        if ($landing->archived) {
            return response([
                "message" => "That landing is archived"
            ], 403);
        }

        return new LandingResource($landing);
    }

    /**
     * Create a new landing.
     *
     * @bodyParam name string required Landing's name
     * @bodyParam product_id int required Landing's product id
     * @bodyParam tag_id int required Landing's tag id
     * @bodyParam url string required Landing's URL
     * @bodyParam country string Country
     *
     * @response 201 {
     *  "message": "Landing created",
     *  "data": [landing],
     * }
     */
    public function store(Request $request)
    {
        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
            'product_id' => 'required|integer|exists:products,id',
            'tag_id' => 'required|integer|exists:tags,id',
            'url' => 'required|url',
            'country' => 'string|min:1',
        ]);

        $landing = Landing::create($params);

        return response([
            "message" => "Landing created",
            "data" => new LandingResource($landing),
        ], 201);
    }



    /**
     * Update the landing's fields.
     *
     * @urlParam id int required Landing's id
     *
     * @bodyParam name string Landing's name
     * @bodyParam product_id int Landing's product id
     * @bodyParam tag_id int Landing's tag id
     * @bodyParam url string Landing's URL
     * @bodyParam country string Country
     *
     * @response {
     *  "data": landing,
     * }
     */
    public function update(Request $request, $id)
    {
        $landing = Landing::findOrFail($id);
        $this->authorize('update', $landing);

        if ($landing->archived) {
            return response([
                "message" => "That landing is archived"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'name' => 'string|min:3',
            'product_id' => 'integer|exists:products,id',
            'tag_id' => 'integer|exists:tags,id',
            'url' => 'url',
            'country' => 'string|min:1',
        ]);

        $landing->update($params);

        return new LandingResource($landing);
    }

    /**
     * Delete the specified landing.
     *
     * @urlParam id int required Landing's id
     *
     * @response {
     *  "message": "Landing has been archived",
     * }
     */
    public function destroy($id)
    {
        $landing = Landing::findOrFail($id);

        $this->authorize('delete', $landing);

        // Archive the landing
        $landing->archived = 1;
        $landing->save();

        return response([
            'message' => 'Landing has been archived'
        ]);
    }
}
