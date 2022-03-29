<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\WarehouseShippingCarrier;
use App\Http\Resources\api\v1\WarehouseShippingCarrierResource;

class WarehouseShippingCarrierController extends Controller
{
    /**
     * Get shipping carriers list.
     *
     * @response {
     *  "data": [carriers],
     * }
     */
    public function index(Request $request)
    {
        // TODO: Authorization

        $objects = WarehouseShippingCarrier::all();

        return WarehouseShippingCarrierResource::collection($objects);
    }

    /**
     * Get shipping carrier's informations.
     *
     * @urlParam id int required Tag's id
     *
     * @response {
     *  "data": tag,
     * }
     */
    public function show($id)
    {
        // TODO: Authorization

        $object = WarehouseShippingCarrier::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That warehouse shipping carrier is archived"
            ], 403);
        }

        return new WarehouseShippingCarrierResource($object);
    }

    /**
     * Create a shipping carrier.
     *
     * @bodyParam name string required Carrier's name
     * @bodyParam slug string required Carrier's slug
     * @bodyParam plan json Carrier's payment plan
     *
     * @response {
     *  "message": "Warehouse shipping carrier successfully created",
     *  "data": carrier,
     * }
     */
    public function store(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'name' => 'required|string|min:1',
            'slug' => 'required|string|min:1',
            'plan' => 'nullable|json',
        ]);

        if (isset($params['plan'])) {
            $params['plan'] = json_decode($params['plan'], true);
        }

        $object = WarehouseShippingCarrier::create($params);

        return response([
            'message' => 'Warehouse shipping carrier successfully created',
            'data' => new WarehouseShippingCarrierResource($object),
        ], 201);
    }
    
    /**
     * Update a shipping carrier.
     *
     * @urlParam id int required Shipping carrier's id
     *
     * @bodyParam name string Carrier's name
     * @bodyParam slug string Carrier's slug
     * @bodyParam plan json Carrier's payment plan
     *
     * @response {
     *  "data": carrier,
     * }
     */
    public function update($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseShippingCarrier::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That warehouse shipping carrier is archived"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'name' => 'string|min:1',
            'slug' => 'string|min:1',
            'plan' => 'nullable|json',
        ]);

        if (isset($params['plan'])) {
            $params['plan'] = json_decode($params['plan'], true);
        }

        $object->update($params);

        return new WarehouseShippingCarrierResource($object);
    }

    /**
     * Archive a shipping carrier.
     *
     * @urlParam id int required Shipping carrier's id
     *
     * @response {
     *  "message": "Warehouse shipping carrier successfully deleted",
     * }
     */
    public function destroy($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseShippingCarrier::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That warehouse shipping carrier is archived"
            ], 403);
        }

        $object->delete();

        return response([
            'message' => 'Warehouse shipping carrier successfully deleted',
        ]);
    }
}
