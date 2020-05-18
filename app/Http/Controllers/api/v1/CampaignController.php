<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Get all campaigns list.
     *
     * @response {
     *  "data": [campaigns],
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', 'App\Campaign');

        return CampaignResource::collection(Campaign::where('archived', 0)->get());
    }

    /**
     * Get campaign's informations.
     *
     * @urlParam id int required Campaign's id
     *
     * @response {
     *  "data": campaign,
     * }
     */
    public function show($id)
    {
        $campaign = Campaign::findOrFail($id);
        $this->authorize('view', $campaign);

        if ($campaign->archived) {
            return response([
                "message" => "That campaign is archived"
            ], 403);
        }

        return new CampaignResource($campaign);
    }

    /**
     * Create a new campaign.
     *
     * @bodyParam name string required Campaign's name
     *
     * @response 201 {
     *  "message": "Campaign created",
     *  "data": [campaign],
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Campaign');

        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
        ]);
        $params['user_id'] = Auth::user()->id;

        $campaign = Campaign::create($params);

        return response([
            "message" => "Campaign created",
            "data" => new CampaignResource($campaign),
        ], 201);
    }



    /**
     * Update the campaign's fields.
     *
     * @urlParam id int required Campaign's id
     *
     * @bodyParam name string Campaign's new name
     *
     * @response {
     *  "data": campaign,
     * }
     */
    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);
        $this->authorize('update', $campaign);

        if ($campaign->archived) {
            return response([
                "message" => "That campaign is archived"
            ], 403);
        }

        // Param validation
        $data = $request->validate([
            'name' => 'string|min:3|max:255',
        ]);

        $campaign->update($data);

        return new CampaignResource($campaign);
    }

    /**
     * Delete the specified campaign.
     *
     * @urlParam id int required Campaign's id
     *
     * @response {
     *  "message": "Campaign has been archived",
     * }
     */
    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);

        $this->authorize('delete', $campaign);

        // Archive the campaign
        $campaign->archived = 1;
        $campaign->save();

        return response([
            'message' => 'Campaign has been archived'
        ]);
    }
}
