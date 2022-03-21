<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\WarehouseProduct;
use App\Http\Resources\api\v1\WarehouseProductResource;

use App\WarehouseProductVariation;
use App\Http\Resources\api\v1\WarehouseProductVariationResource;

class WarehouseProductController extends Controller
{
    /**
     * Get warehouse products list.
     *
     * @response {
     *  "data": [warehouse_products],
     * }
     */
    public function index()
    {
        // TODO: Authorization

        $objects = WarehouseProduct::query();

        return WarehouseProductResource::collection($objects->get());
    }

    /**
     * Get warehouse product's informations.
     *
     * @urlParam id int required Warehouse product's id
     *
     * @response {
     *  "data": warehouse_product,
     * }
     */
    public function show($id)
    {
        // TODO: Authorization

        $object = WarehouseProduct::findOrFail($id);

        return new WarehouseProductResource($object);
    }

    /**
     * Create a warehouse product.
     *
     * @bodyParam name string required Warehouse product's name
     * @bodyParam sku string Warehouse product's sku
     *
     * @response {
     *  "message": "Warehouse product successfully created",
     *  "data": warehouse_product,
     * }
     */
    public function store(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'name' => 'required|string|min:1',
            'sku'  => 'nullable|string|min:1|unique:warehouse_products,sku',
        ]);

        $object = WarehouseProduct::create($params);

        return response([
            'message' => 'Warehouse product successfully created',
            'data' => new WarehouseProductResource($object),
        ], 201);
    }

    /**
     * Update a warehouse product.
     *
     * @urlParam id int required Warehouse product's id
     *
     * @bodyParam name string required Warehouse product's name
     * @bodyParam sku string Warehouse product's sku
     *
     * @response {
     *  "data": warehouse_product,
     * }
     */
    public function update($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseProduct::findOrFail($id);

        // Param validation
        $params = $request->validate([
            'name' => 'string|min:1',
            'sku'  => 'nullable|string|min:1|unique:warehouse_products,sku',
        ]);

        $object->update($params);

        return new WarehouseProductResource($object);
    }

    /**
     * Archive a warehouse product.
     *
     * @urlParam id int required Warehouse product's id
     *
     * @response {
     *  "message": "Warehouse product successfully deleted",
     * }
     */
    public function destroy($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseProduct::findOrFail($id);

        $object->delete();

        return response([
            'message' => 'Warehouse product successfully deleted',
        ]);
    }

    /**
     * Create a varation for the specified warehouse product.
     *
     * @bodyParam name string required Product variation's name
     * @bodyParam sku string required Product variation's sku
     * @bodyParam price float required Product variation's price
     * @bodyParam quantity int Product variation's stock quantity
     *
     * @response {
     *  "message": "Warehouse product variation successfully created",
     *  "data": warehouse_product_variation,
     * }
     */
    public function storeVariation($product_id, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'name'     => 'required|string|min:1',
            'sku'      => 'required|string|min:1|unique:warehouse_product_variations,sku',
            'price'    => 'required|numeric|min:0',
            'quantity' => 'integer|min:0',
        ]);

        $params['warehouse_product_id'] = $product_id;
        $object = WarehouseProductVariation::create($params);

        return response([
            'message' => 'Warehouse product variation successfully created',
            'data' => new WarehouseProductVariationResource($object),
        ], 201);
    }

    /**
     * Update the specified warehouse product's varation.
     *
     * @urlParam id int required Variation's id
     *
     * @bodyParam name string Product variation's name
     * @bodyParam sku string Product variation's sku
     * @bodyParam price float Product variation's price
     * @bodyParam quantity int Product variation's stock quantity
     *
     * @response {
     *  "data": warehouse_product_variation,
     * }
     */
    public function updateVariation($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseProductVariation::findOrFail($id);

        // Param validation
        $params = $request->validate([
            'name'     => 'string|min:1',
            'sku'      => 'string|min:1|unique:warehouse_product_variations,sku',
            'price'    => 'numeric|min:0',
            'quantity' => 'integer|min:0',
        ]);

        $object->update($params);

        return new WarehouseProductVariationResource($object);
    }

    /**
     * Delete the specified warehouse product's varation.
     *
     * @urlParam id int required Variation's id
     *
     * @response {
     *  "message": "Warehouse product variation successfully deleted",
     * }
     */
    public function destroyVariation($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseProductVariation::findOrFail($id);

        $object->delete();

        return response([
            'message' => 'Warehouse product variation successfully deleted',
        ]);
    }

}
