<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Advertiser as Advertiser;
use App\Http\Resources\api\v1\AdvertiserResource;

class AdvertiserController extends Controller
{
    /**
     * Get all advertisers list.
     *
     * @response {
     *  "data": [advertisers],
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', 'App\Advertiser');

        return AdvertiserResource::collection(Advertiser::where('archived', 0)->get());
    }

    /**
     * Get advertiser's informations.
     *
     * @urlParam id int required Advertiser's id
     *
     * @response {
     *  "data": advertiser,
     * }
     */
    public function show($id)
    {
        $advertiser = Advertiser::findOrFail($id);
        $this->authorize('view', $advertiser);

        if ($advertiser->archived) {
            return response([
                "message" => "That advertiser is archived"
            ], 403);
        }

        return new AdvertiserResource($advertiser);
    }

    /**
     * Create a new advertiser.
     *
     * @bodyParam name string required Advertiser's name
     *
     * @response 201 {
     *  "message": "Advertiser created",
     *  "data": advertiser,
     * }
     */
    public function store(Request $request)
    {
        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
        ]);

        $advertiser = Advertiser::create($params);

        return response([
            "message" => "Advertiser created",
            "data" => new AdvertiserResource($advertiser),
        ], 201);
    }



    /**
     * Update the advertiser's fields.
     *
     * @urlParam id int required Advertiser's id
     *
     * @bodyParam name string Advertiser's name
     *
     * @response {
     *  "data": advertiser,
     * }
     */
    public function update(Request $request, $id)
    {
        $advertiser = Advertiser::findOrFail($id);
        $this->authorize('update', $advertiser);

        if ($advertiser->archived) {
            return response([
                "message" => "That advertiser is archived"
            ], 403);
        }

        // Params validation
        $params = $request->validate([
            'name' => 'string|min:3',
        ]);

        $advertiser->update($params);

        return new AdvertiserResource($advertiser);
    }

    /**
     * Delete the specified advertiser.
     *
     * @urlParam id int required Advertiser's id
     *
     * @response {
     *  "message": "Advertiser has been archived",
     * }
     */
    public function destroy($id)
    {
        $advertiser = Advertiser::findOrFail($id);

        $this->authorize('delete', $advertiser);

        // Archive the advertiser
        $advertiser->archived = 1;
        $advertiser->save();

        return response([
            'message' => 'Advertiser has been archived'
        ]);
    }
}
