<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\WarehouseOrder;
use App\Http\Resources\api\v1\WarehouseOrderResource;

class WarehouseOrderController extends Controller
{
    /**
     * Get warehouse orders list.
     *
     * @response {
     *  "data": [warehouse_orders],
     * }
     */
    public function index(Request $request)
    {
        // TODO: Authorization

        $objects = WarehouseOrder::query();
        $objects = $objects->where('status', '<>', 'deleted');

        return WarehouseOrderResource::collection($objects->get());
    }

    /**
     * Get warehouse order's informations.
     *
     * @urlParam id int required Order's id
     *
     * @response {
     *  "data": warehouse_order,
     * }
     */
    public function show($id)
    {
        // TODO: Authorization

        $object = WarehouseOrder::findOrFail($id);
        if ($object->status == 'deleted') {
            return response([
                "message" => "That warehouse order is deleted"
            ], 403);
        }

        return new WarehouseOrderResource($object);
    }

    /**
     * Create a warehouse order.
     *
     * @bodyParam customer_id int required Customer's id
     * @bodyParam amount string required Warehouse order's amount
     * @bodyParam status string Warehouse order's status
     *
     * @response {
     *  "message": "Warehouse order successfully created",
     *  "data": warehouse_order,
     * }
     */
    public function store(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'customer_id' => 'required|integer|min:1|exists:customers,id',
            'amount'      => 'required|numeric|min:0',
            'status'      => 'in:' . implode(WarehouseOrder::STATUSES, ','),
        ]);

        $object = WarehouseOrder::create($params);

        return response([
            'message' => 'Warehouse order successfully created',
            'data' => new WarehouseOrderResource($object),
        ], 201);
    }
    
    /**
     * Update a warehouse order.
     *
     * @urlParam id int required Warehouse order's id
     *
     * @bodyParam amount string Warehouse order's first name
     * @bodyParam status string Warehouse order's last name
     *
     * @response {
     *  "data": warehouse_order,
     * }
     */
    public function update($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseOrder::findOrFail($id);
        if ($object->status == 'deleted') {
            return response([
                "message" => "That warehouse order is deleted"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'amount'      => 'numeric|min:0',
            'status'      => 'in:' . implode(WarehouseOrder::STATUSES, ','),
        ]);

        $object->update($params);

        return new WarehouseOrderResource($object);
    }

    /**
     * Archive a warehouse order.
     *
     * @urlParam id int required Warehouse order's id
     *
     * @response {
     *  "message": "Warehouse order successfully deleted",
     * }
     */
    public function destroy($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseOrder::findOrFail($id);
        if ($object->status == 'deleted') {
            return response([
                "message" => "That warehouse order is deleted"
            ], 403);
        }

        $object->status = 'deleted';
        $object->save();

        return response([
            'message' => 'Warehouse order successfully deleted',
        ]);
    }

}
