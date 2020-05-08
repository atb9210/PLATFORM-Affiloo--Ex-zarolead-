<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Order as Order;
use App\Http\Resources\api\v1\OrderResource;

class OrderController extends Controller {
    /**
     * Get all orders list.
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     * @urlParam filter_status int Order's status id
     * @urlParam filter_product int Order's product id
     *
     * @response {
     *  "data": [orders],
     * }
     */
    public function index(Request $request) {
        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
            'filter_status' => 'nullable|integer|min:0',
            'filter_product' => 'nullable|integer|min:0',
        ]);

        // Query orders (applying filters)
        $orders = Order::query();
        $orders = $orders->where('archived', 0);
        if (!Auth::user()->can('viewAny', 'App\Order')) {
            $orders = $orders->where('user_id', Auth::user()->id);
        }
        if (isset($params['filter_date_from'])) {
            $orders = $orders->where('created_at', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $orders = $orders->where('created_at', '<=', $params['filter_date_to']);
        }
        if (isset($params['filter_status'])) {
            $orders = $orders->where('order_status_id', $params['filter_status']);
        }
        if (isset($params['filter_product'])) {
            $orders = $orders->where('product_id', $params['filter_product']);
        }
        $orders = $orders->orderBy('created_at', 'desc');

        return OrderResource::collection($orders->get());
    }

    /**
     * Get order's informations.
     *
     * @urlParam id int required Order's id
     *
     * @response {
     *  "data": order,
     * }
     */
    public function show($id) {
        $order = Order::findOrFail($id);
        $this->authorize('view', $order);


        return new OrderResource($order);
    }

    /**
     * Create a new order.
     *
     * @bodyParam user_id int required Order's user id
     * @bodyParam name string required Customer's name
     * @bodyParam phone string required Customer's phone number
     * @bodyParam address string required Customer's address
     * @bodyParam product_id int required Order's product id
     * @bodyParam quantity string required Quantity
     * @bodyParam commission float required Commission
     * @bodyParam sub_commission float required Sub commission
     * @bodyParam network string required Network name
     *
     * @response {
     *  "data": order
     * }
     */
    public function store(Request $request) {
        // Params validation
        $params = $request->validate([
            'user_id' => 'required|integer|min:1',
            'name' => 'required|string|min:3',
            'phone' => 'required|string|min:3',
            'address' => 'required|string|min:3',
            'product_id' => 'required|integer|min:1',
            'quantity' => 'required|string|min:1',
            'commission' => 'required|numeric|min:1',
            'sub_commission' => 'required|numeric|min:1',
        ]);
    }

    /**
     * Update the order's fields.
     *
     * @urlParam id int required Order's id
     *
     * @bodyParam name string Order's new name
     * @bodyParam email string Order's new email
     *
     * @response {
     *  "data": order
     * }
     */
    public function update(Request $request, $id) {
        $order = Order::findOrFail($id);

        $this->authorize('update', $order);

        // Param validation
        $params = $request->validate([
            'name' => 'string|min:3|max:255',
            'email' => 'email|min:4|max:255',
        ]);

        $order->update($params);

        return response([
            "order" => $order,
        ]);    
    }

    /**
     * Archive the specified order.
     *
     * @urlParam id int required Order's id
     *
     * @response {
     *  "message": "Order has been archived",
     * }
     */
    public function destroy($id) {
        $order = Order::findOrFail($id);

        $this->authorize('delete', $order);

        // Archive the order
        $order->archived = 1;
        $order->save();

        return response([
            "message" => "Order has been archived"
        ]);
    }
}
