<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\TrafficSource as TrafficSource;
use App\Http\Resources\api\v1\TrafficSourceResource;

class TrafficSourceController extends Controller
{
    /**
     * Get all traffic sources list.
     *
     * @response {
     *  "data": [traffic_sources],
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', 'App\TrafficSource');

        return TrafficSourceResource::collection(TrafficSource::where('archived', 0)->get());
    }

    /**
     * Get traffic source's informations.
     *
     * @urlParam id int required Traffic source's id
     *
     * @response {
     *  "data": traffic_source,
     * }
     */
    public function show($id)
    {
        $traffic_source = TrafficSource::findOrFail($id);
        $this->authorize('view', $traffic_source);

        if ($traffic_source->archived) {
            return response([
                "message" => "That traffic source is archived"
            ], 403);
        }

        return new TrafficSourceResource($traffic_source);
    }

    /**
     * Create a new traffic source.
     *
     * @bodyParam name string required Traffic source's name
     * @bodyParam custom_params json Custom params
     *
     * @response 201 {
     *  "message": "Traffic source created",
     *  "data": [traffic_source],
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\TrafficSource');

        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
            'custom_params' => 'json',
        ]);

        if (isset($params['custom_params'])) {
            $params['custom_params'] = json_decode($params['custom_params']);
        } else {
            $params['custom_params'] = [];
        }

        $traffic_source = TrafficSource::create($params);

        return response([
            "message" => "Traffic source created",
            "data" => new TrafficSourceResource($traffic_source),
        ], 201);
    }



    /**
     * Update the traffic source's fields.
     *
     * @urlParam id int required Traffic source's id
     *
     * @bodyParam name string Traffic source's name
     * @bodyParam custom_params json Custom params
     *
     * @response {
     *  "data": traffic_source,
     * }
     */
    public function update(Request $request, $id)
    {
        $traffic_source = TrafficSource::findOrFail($id);
        $this->authorize('update', $traffic_source);

        if ($traffic_source->archived) {
            return response([
                "message" => "That traffic source is archived"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'name' => 'string|min:3',
            'custom_params' => 'json',
        ]);

        if (isset($params['custom_params'])) {
            $params['custom_params'] = json_decode($params['custom_params']);
        }

        $traffic_source->update($params);

        return new TrafficSourceResource($traffic_source);
    }

    /**
     * Delete the specified traffic source.
     *
     * @urlParam id int required Traffic source's id
     *
     * @response {
     *  "message": "Traffic source has been archived",
     * }
     */
    public function destroy($id)
    {
        $traffic_source = TrafficSource::findOrFail($id);

        $this->authorize('delete', $traffic_source);

        // Archive the traffic_source
        $traffic_source->archived = 1;
        $traffic_source->save();

        return response([
            'message' => 'Traffic source has been archived'
        ]);
    }

    /**
     * Traffic source validate params.
     *
     * @urlParam id int required Traffic source's id
     *
     * @bodyParam params json required Traffic source's params to validate
     *
     * @response {
     *  "data": true,
     * }
     */
    public function validateAdModel($id, Request $request)
    {
        $traffic_source = TrafficSource::findOrFail($id);

        // Params validation
        $params = $request->validate([
            'params' => 'required|json',
        ]);
        $params['params'] = json_decode($params['params'], TRUE);

        $rules = $traffic_source->getAdModel('validation.params');
        $validator = Validator::make($params['params'], $rules);
        if ($validator->fails()) {
            return response([
                'message' => 'The given data was invalid.',
                'errors' => $validator->messages(),
            ], 422);
        }
        return response([
            'data' => true
        ]);
    }
}
