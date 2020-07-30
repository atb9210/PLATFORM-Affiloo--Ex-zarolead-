<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Landing as Landing;
use App\LandingStats as LandingStats;
use App\Http\Resources\api\v1\LandingResource;

class LandingController extends Controller
{
    /**
     * Get all landings list.
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     *
     * @response {
     *  "data": [landings],
     * }
     */
    public function index(Request $request)
    {
        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
        ]);

        $landings = Landing::query();
        $landings = $landings->where('archived', 0);
        if (!Auth::user()->can('viewAny', 'App\Landing')) {
            $landings = $landings->where('user_id', Auth::user()->id);
        }

        return LandingResource::collection($landings->get());
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
     * @bodyParam campaign_id int required Landing's campaign id
     * @bodyParam form_id int required Landing's form id
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
        $this->authorize('create', 'App\Landing');

        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
            'campaign_id' => 'required|integer|exists:campaigns,id',
            'form_id' => 'required|integer|exists:forms,id',
            'tag_id' => 'required|integer|exists:tags,id',
            'url' => 'required|url',
            'country' => 'string|min:1',
        ]);
        $params['user_id'] = Auth::user()->id;

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
     * @bodyParam campaign_id int Landing's campaign id
     * @bodyParam form_id int Landing's form id
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
            'campaign_id' => 'integer|exists:campaigns,id',
            'form_id' => 'integer|exists:forms,id',
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

    /**
     * Get landing stats.
     *
     * @urlParam id int required Landing's id
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     *
     * @response {
     *  "data": [stats]
     * }
     */
    public function stats($id, Request $request)
    {
        $form = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
        ]);

        $stats = LandingStats::where('form_id', $id);
        if (isset($params['filter_date_from'])) {
            $stats = $stats->where('day', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $stats = $stats->where('day', '<=', $params['filter_date_to']);
        }
        $stats = $stats->orderBy('day', 'desc');

        return LandingStatsResource::collection($stats->get());
    }
}
