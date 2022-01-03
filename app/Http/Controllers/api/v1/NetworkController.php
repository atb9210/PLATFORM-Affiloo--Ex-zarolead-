<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Network as Network;
use App\Http\Resources\api\v1\NetworkResource;

class NetworkController extends Controller
{
    /**
     * Get all networks list.
     *
     * @response {
     *  "data": [networks],
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', 'App\Network');

        return NetworkResource::collection(Network::where('archived', 0)->get());
    }

    /**
     * Get network's informations.
     *
     * @urlParam id int required Network's id
     *
     * @response {
     *  "data": network,
     * }
     */
    public function show($id)
    {
        $network = Network::findOrFail($id);
        $this->authorize('view', $network);

        if ($network->archived) {
            return response([
                "message" => "That network is archived"
            ], 403);
        }

        return new NetworkResource($network);
    }

    /**
     * Create a new network.
     *
     * @bodyParam name string required Network's name
     *
     * @response 201 {
     *  "message": "Network created",
     *  "data": network,
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Network');

        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
        ]);

        $network = Network::create($params);

        return response([
            "message" => "Network created",
            "data" => new NetworkResource($network),
        ], 201);
    }



    /**
     * Update the network's fields.
     *
     * @urlParam id int required Network's id
     *
     * @bodyParam name string Network's name
     *
     * @response {
     *  "data": network,
     * }
     */
    public function update(Request $request, $id)
    {
        $network = Network::findOrFail($id);
        $this->authorize('update', $network);

        if ($network->archived) {
            return response([
                "message" => "That network is archived"
            ], 403);
        }

        // Params validation
        $params = $request->validate([
            'name' => 'string|min:3',
            'enabled' => 'boolean',
        ]);

        $network->update($params);

        return new NetworkResource($network);
    }

    /**
     * Delete the specified network.
     *
     * @urlParam id int required Network's id
     *
     * @response {
     *  "message": "Network has been archived",
     * }
     */
    public function destroy($id)
    {
        $network = Network::findOrFail($id);

        $this->authorize('delete', $network);

        // Archive the network
        $network->archived = 1;
        $network->save();

        return response([
            'message' => 'Network has been archived'
        ]);
    }

    /**
     * Network validate params.
     *
     * @urlParam id int required Network's id
     *
     * @bodyParam network_params json required Network's params to validate
     *
     * @response {
     *  "data": true,
     * }
     */
    public function validateParams($id, Request $request)
    {
        $network = Network::findOrFail($id);

        // Params validation
        $params = $request->validate([
            'network_params' => 'required|json',
        ]);
        $params['network_params'] = json_decode($params['network_params'], TRUE);

        $rules = $network->getApi('form.validation.params');
        $validator = Validator::make($params['network_params'], $rules);
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
