<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        $products = Product::query();
        if (!Auth::user()->can('viewAny', 'App\Product')) {
            if (Auth::user()->hasRole('advertiser')) {
                $products = $products->where('advertiser_id', Auth::user()->id);
            } else {
                $products = $products->where('user_id', Auth::user()->id);
            }
        }
        $products = $products->where('archived', 0);

        return ProductResource::collection($products->get());
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
     * @bodyParam quantity json required Product's quantity JSON
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
            'advertiser_id' => 'required|integer|exists:users,id',
            'country' => 'string|min:1',
            'quantity' => 'required|json',
        ]);
        $params['user_id'] = Auth::user()->id;

        $params['quantity'] = json_decode($params['quantity'], TRUE);

        // Validate quantity JSON
        $validator = Validator::make($params['quantity'], [
            'options' => 'required|array',
            'default' => 'required',
        ]);
        if ($validator->fails()) {
            return response([
                'message' => 'The given data - for the quantity JSON param - was invalid.',
                'errors' => $validator->messages(),
            ], 422);
        }

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
     * @bodyParam advertiser_id int Product's new user ID
     * @bodyParam country string Product's new country
     * @bodyParam quantity json Product's quantity JSON
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
        $params = $request->validate([
            'name' => 'string|min:3|max:255',
            'tag_id' => 'integer|min:1|exists:tags,id',
            'advertiser_id' => 'integer|min:1|exists:users,id',
            'country' => 'string|min:1|max:255',
            'quantity' => 'json',
        ]);
        if (isset($params['quantity'])) {
            $params['quantity'] = json_decode($params['quantity'], TRUE);
        }

        // Validate quantity JSON
        if (isset($params['quantity'])) {
            $validator = Validator::make($params['quantity'], [
                'options' => 'required|array',
                'default' => 'required',
            ]);
            if ($validator->fails()) {
                return response([
                    'message' => 'The given data - for the quantity JSON param - was invalid.',
                    'errors' => $validator->messages(),
                ], 422);
            }
        }

        $product->update($params);

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
