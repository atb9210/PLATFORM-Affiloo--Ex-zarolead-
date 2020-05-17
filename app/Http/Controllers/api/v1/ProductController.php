<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Product as Product;
use App\Http\Resources\api\v1\ProductResource;

class ProductController extends Controller
{
    /**
     * Get all products list.
     *
     * @response {
     *  "data": [products],
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', 'App\Product');

        return ProductResource::collection(Product::where('archived', 0)->get());
    }

    /**
     * Get product's informations.
     *
     * @urlParam id int required Product's id
     *
     * @response {
     *  "data": product,
     * }
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('view', $product);

        if ($product->archived) {
            return response([
                "message" => "That product is archived"
            ], 403);
        }

        return new ProductResource($product);
    }

    /**
     * Create a new product.
     *
     * @bodyParam name string required Product's name
     * @bodyParam tag_id int required Product's tag id
     * @bodyParam advertiser_id int required Product's advertiser id
     * @bodyParam country string Country
     *
     * @response 201 {
     *  "message": "Product created",
     *  "data": [product],
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Product');

        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
            'tag_id' => 'required|integer|exists:tags,id',
            'advertiser_id' => 'required|integer|exists:advertisers,id',
            'country' => 'string|min:1',
        ]);

        $product = Product::create($params);

        return response([
            "message" => "Product created",
            "data" => new ProductResource($product),
        ], 201);
    }



    /**
     * Update the product's fields.
     *
     * @urlParam id int required Product's id
     *
     * @bodyParam name string Product's new name
     * @bodyParam tag_id int Product's new tag ID
     * @bodyParam advertiser_id int Product's new advertiser ID
     * @bodyParam country string Product's new country
     *
     * @response {
     *  "data": product,
     * }
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);

        if ($product->archived) {
            return response([
                "message" => "That product is archived"
            ], 403);
        }

        // Param validation
        $data = $request->validate([
            'name' => 'string|min:3|max:255',
            'tag_id' => 'integer|min:1|exists:tags,id',
            'advertiser_id' => 'integer|min:1|exists:advertisers,id',
            'country' => 'string|min:1|max:255',
        ]);

        $product->update($data);

        return new ProductResource($product);
    }

    /**
     * Delete the specified product.
     *
     * @urlParam id int required Product's id
     *
     * @response {
     *  "message": "Product has been archived",
     * }
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $this->authorize('delete', $product);

        // Archive the product
        $product->archived = 1;
        $product->save();

        return response([
            'message' => 'Product has been archived'
        ]);
    }
}
