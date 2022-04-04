<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\WarehouseLead;
use App\Http\Resources\api\v1\WarehouseLeadResource;

class WarehouseLeadController extends Controller
{
    /**
     * Get warehouse leads list.
     *
     * @response {
     *  "data": [warehouse_leads],
     * }
     */
    public function index(Request $request)
    {
        // TODO: Authorization

        $objects = WarehouseLead::query();
        $objects = $objects->where('archived', 0);

        return WarehouseLeadResource::collection($objects->get());
    }

    /**
     * Get warehouse lead's informations.
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

        $object = WarehouseLead::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That warehouse lead is archived"
            ], 403);
        }

        return new WarehouseLeadResource($object);
    }

    /**
     * Create a warehouse lead.
     *
     * @bodyParam first_name string required Warehouse lead's first name
     * @bodyParam last_name string required Warehouse lead's last name
     * @bodyParam phone string Warehouse lead's phone number
     * @bodyParam email string Warehouse lead's email
     * @bodyParam address_line1 string Warehouse lead's address_line1
     * @bodyParam address_line2 string Warehouse lead's address_line2
     * @bodyParam city string Warehouse lead's city
     * @bodyParam postcode string Warehouse lead's postcode
     * @bodyParam zone string Warehouse lead's zone
     * @bodyParam country string Warehouse lead's country
     * @bodyParam info json Warehouse lead's info
     *
     * @response {
     *  "message": "Warehouse lead successfully created",
     *  "data": warehouse_lead,
     * }
     */
    public function store(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'first_name'    => 'required|string|min:1',
            'last_name'     => 'required|string|min:1',
            'phone'         => 'nullable|string|min:7',
            'email'         => 'nullable|string',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'city'          => 'nullable|string',
            'postcode'      => 'nullable|string',
            'zone'          => 'nullable|string',
            'country'       => 'nullable|string',
            'info'          => 'nullable|json',
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        $object = WarehouseLead::create($params);

        return response([
            'message' => 'Warehouse lead successfully created',
            'data' => new WarehouseLeadResource($object),
        ], 201);
    }
    
    /**
     * Update a warehouse lead.
     *
     * @urlParam id int required Warehouse lead's id
     *
     * @bodyParam first_name string Warehouse lead's first name
     * @bodyParam last_name string Warehouse lead's last name
     * @bodyParam phone string Warehouse lead's phone number
     * @bodyParam email string Warehouse lead's email
     * @bodyParam address_line1 string Warehouse lead's address_line1
     * @bodyParam address_line2 string Warehouse lead's address_line2
     * @bodyParam city string Warehouse lead's city
     * @bodyParam postcode string Warehouse lead's postcode
     * @bodyParam zone string Warehouse lead's zone
     * @bodyParam country string Warehouse lead's country
     * @bodyParam info json Warehouse lead's info
     *
     * @response {
     *  "data": warehouse_lead,
     * }
     */
    public function update($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseLead::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That warehouse lead is archived"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'first_name'    => 'string|min:1',
            'last_name'     => 'string|min:1',
            'phone'         => 'string|min:7',
            'email'         => 'nullable|string',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'city'          => 'nullable|string',
            'postcode'      => 'nullable|string',
            'zone'          => 'nullable|string',
            'country'       => 'nullable|string',
            'info'          => 'nullable|json',
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        $object->update($params);

        return new WarehouseLeadResource($object);
    }

    /**
     * Archive a warehouse lead.
     *
     * @urlParam id int required Warehouse lead's id
     *
     * @response {
     *  "message": "Warehouse lead successfully deleted",
     * }
     */
    public function destroy($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseLead::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That warehouse lead is archived"
            ], 403);
        }

        $object->archived = 1;
        $object->save();

        return response([
            'message' => 'Warehouse lead successfully deleted',
        ]);
    }

}
