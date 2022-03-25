<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\WarehouseOrder;
use App\Http\Resources\api\v1\WarehouseOrderResource;

use App\WarehouseOrderLine;
use App\Http\Resources\api\v1\WarehouseOrderLineResource;

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
     * @bodyParam amount float required Warehouse order's amount
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
            'status'      => 'in:' . join(',', WarehouseOrder::STATUSES),
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
     * @bodyParam amount float Warehouse order's first name
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
            'status'      => 'in:' . join(',', WarehouseOrder::STATUSES),
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

    /**
     * Get warehouse order lines.
     *
     * @urlParam warehouse_order_id int required Warehouse order's id
     *
     * @response {
     *  "data": [warehouse_order_lines]
     * }
     */
    public function lines($warehouse_order_id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseOrder::findOrFail($warehouse_order_id);

        return WarehouseOrderLineResource::collection($object->lines);
    }

    /**
     * Create a warehouse order line.
     *
     * @urlParam warehouse_order_id int required Warehouse order's id
     *
     * @bodyParam warehouse_product_variation_id int required Warehouse product variation's id
     * @bodyParam quantity int required Warehouse order line's quantity
     * @bodyParam unit_price float required Warehouse order line's unit price
     * @bodyParam total_price float required Warehouse order line's total price
     * @bodyParam discount float required Warehouse order line's discount
     * @bodyParam final_price float required Warehouse order line's final_price
     * @bodyParam attributes json Warehouse order line's attributes
     *
     * @response {
     *  "message": "Warehouse order line successfully created",
     *  "data": warehouse_order_line,
     * }
     */
    public function storeLine($warehouse_order_id, Request $request)
    {
        // TODO: Authorization

        $order = WarehouseOrder::findOrFail($warehouse_order_id);

        // Param validation
        $params = $request->validate([
            'warehouse_product_variation_id' => 'required|integer|min:1|exists:warehouse_product_variations,id',
            'quantity'    => 'required|integer|min:1',
            'unit_price'  => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'discount'    => 'required|numeric|min:0',
            'final_price' => 'required|numeric|min:0',
            'attributes'  => 'nullable|json',
        ]);

        if (isset($params['attributes'])) {
            $params['attributes'] = json_decode($params['attributes'], true);
        }

        $params['warehouse_order_id'] = $warehouse_order_id;

        $object = WarehouseOrderLine::create($params);

        return response([
            'message' => 'Warehouse order line successfully created',
            'data' => new WarehouseOrderLineResource($object),
        ], 201);
    }
    
    /**
     * Update a warehouse order line.
     *
     * @urlParam id int required Warehouse order line's id
     *
     * @bodyParam quantity int Warehouse order line's quantity
     * @bodyParam unit_price float Warehouse order line's unit price
     * @bodyParam total_price float Warehouse order line's total price
     * @bodyParam discount float Warehouse order line's discount
     * @bodyParam final_price float Warehouse order line's final_price
     * @bodyParam attributes json Warehouse order line's attributes
     *
     * @response {
     *  "data": warehouse_order_line,
     * }
     */
    public function updateLine($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseOrderLine::findOrFail($id);

        // Param validation
        $params = $request->validate([
            'quantity'    => 'integer|min:1',
            'unit_price'  => 'numeric|min:0',
            'total_price' => 'numeric|min:0',
            'discount'    => 'numeric|min:0',
            'final_price' => 'numeric|min:0',
            'attributes'  => 'nullable|json',
        ]);

        if (isset($params['attributes'])) {
            $params['attributes'] = json_decode($params['attributes'], true);
        }

        $object->update($params);

        return new WarehouseOrderResource($object);
    }

    /**
     * Delete a warehouse order line.
     *
     * @urlParam id int required Warehouse order line's id
     *
     * @response {
     *  "message": "Warehouse order line successfully deleted",
     * }
     */
    public function destroyLine($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseOrderLine::findOrFail($id);

        $object->delete();

        return response([
            'message' => 'Warehouse order line successfully deleted',
        ]);
    }

}
