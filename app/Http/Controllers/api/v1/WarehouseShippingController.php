<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\WarehouseShipping;
use App\Http\Resources\api\v1\WarehouseShippingResource;

class WarehouseShippingController extends Controller
{
    /**
     * Get shippings list.
     *
     * @response {
     *  "data": [shippings],
     * }
     */
    public function index(Request $request)
    {
        // TODO: Authorization

        $objects = WarehouseShipping::query();
        $objects = $objects->where('archived', 0);

        return WarehouseShippingResource::collection($objects->get());
    }

    /**
     * Get shipping's informations.
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

        $object = WarehouseShipping::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That warehouse shipping is archived"
            ], 403);
        }

        return new WarehouseShippingResource($object);
    }

    /**
     * Create a shipping.
     *
     * @bodyParam warehouse_order_id int required Order relative to this shipping
     * @bodyParam warehouse_shipping_carrier_id int required Carrier relative to this shipping
     * @bodyParam status string Shipping's status
     * @bodyParam first_name string required Shipping's first name
     * @bodyParam last_name string required Shipping's last name
     * @bodyParam phone string Shipping's phone number
     * @bodyParam email string Shipping's email
     * @bodyParam address_line1 string required Shipping's address_line1
     * @bodyParam address_line2 string Shipping's address_line2
     * @bodyParam city string required Shipping's city
     * @bodyParam postcode string required Shipping's postcode
     * @bodyParam zone string required Shipping's zone
     * @bodyParam country string required Shipping's country
     * @bodyParam notes string Shipping's notes
     * @bodyParam info json Shipping's info
     *
     * @response {
     *  "message": "Warehouse shipping successfully created",
     *  "data": shipping,
     * }
     */
    public function store(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'warehouse_order_id'            => 'required|integer|exists:warehouse_orders,id',
            'warehouse_shipping_carrier_id' => 'required|integer|exists:warehouse_shipping_carriers,id',
            'status'        => 'in:' . join(',', WarehouseShipping::STATUSES),
            'first_name'    => 'required|string|min:1',
            'last_name'     => 'required|string|min:1',
            'phone'         => 'nullable|string|min:7',
            'email'         => 'nullable|string',
            'address_line1' => 'required|string',
            'address_line2' => 'nullable|string',
            'city'          => 'required|string',
            'postcode'      => 'required|string',
            'zone'          => 'required|string',
            'country'       => 'required|string',
            'notes'         => 'nullable|string',
            'info'          => 'nullable|json',
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        $object = WarehouseShipping::create($params);

        return response([
            'message' => 'Warehouse shipping successfully created',
            'data' => new WarehouseShippingResource($object),
        ], 201);
    }
    
    /**
     * Update a shipping.
     *
     * @urlParam id int required Shipping's id
     *
     * @bodyParam status string Shipping's status
     * @bodyParam first_name string Shipping's first name
     * @bodyParam last_name string Shipping's last name
     * @bodyParam phone string Shipping's phone number
     * @bodyParam email string Shipping's email
     * @bodyParam address_line1 string Shipping's address_line1
     * @bodyParam address_line2 string Shipping's address_line2
     * @bodyParam city string Shipping's city
     * @bodyParam postcode string Shipping's postcode
     * @bodyParam zone string Shipping's zone
     * @bodyParam country string Shipping's country
     * @bodyParam notes string Shipping's notes
     * @bodyParam info json Shipping's info
     *
     * @response {
     *  "data": shipping,
     * }
     */
    public function update($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseShipping::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That shipping is archived"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'status'        => 'in:' . join(',', WarehouseShipping::STATUSES),
            'first_name'    => 'string|min:1',
            'last_name'     => 'string|min:1',
            'phone'         => 'nullable|string|min:7',
            'email'         => 'nullable|string',
            'address_line1' => 'string',
            'address_line2' => 'nullable|string',
            'city'          => 'string',
            'postcode'      => 'string',
            'zone'          => 'string',
            'country'       => 'string',
            'notes'         => 'nullable|string',
            'info'          => 'nullable|json',
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        $object->update($params);

        return new WarehouseShippingResource($object);
    }

    /**
     * Archive a shipping.
     *
     * @urlParam id int required Shipping's id
     *
     * @response {
     *  "message": "Warehouse shipping successfully deleted",
     * }
     */
    public function destroy($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseShipping::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That warehouse shipping is archived"
            ], 403);
        }

        $object->archived = 1;
        $object->save();

        return response([
            'message' => 'Warehouse shipping successfully deleted',
        ]);
    }
}
