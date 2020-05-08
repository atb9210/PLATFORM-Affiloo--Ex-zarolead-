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
        $data = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
            'filter_status' => 'nullable|integer|min:0',
            'filter_product' => 'nullable|integer|min:0',
        ]);

        // Query orders (applying filters)
        $orders = Order::query();
        if (!Auth::user()->can('viewAny', 'App\Order')) {
            $orders = $orders->where('user_id', Auth::user()->id);
        }
        if (isset($data['filter_date_from'])) {
            $orders = $orders->where('created_at', '>=', $data['filter_date_from']);
        }
        if (isset($data['filter_date_to'])) {
            $orders = $orders->where('created_at', '<=', $data['filter_date_to']);
        }
        if (isset($data['filter_status'])) {
            $orders = $orders->where('order_status_id', $data['filter_status']);
        }
        if (isset($data['filter_product'])) {
            $orders = $orders->where('product_id', $data['filter_product']);
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

        return response([
            "order" => $order,
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
        $data = $request->validate([
            'name' => 'string|min:3|max:255',
            'email' => 'email|min:4|max:255',
        ]);

        $order->update($data);

        return response([
            "order" => $order,
        ]);    
    }

    /**
     * Delete the specified order.
     *
     * @urlParam id int required Order's id
     *
     * @response {
     *  "message": "Order has been deleted",
     * }
     */
    public function destroy($id) {
        $order = Order::findOrFail($id);

        $this->authorize('delete', $order);

        // Delete the order
        $order->delete();

        return response([
            "message" => "Order has been deleted"
        ]);
    }
}
