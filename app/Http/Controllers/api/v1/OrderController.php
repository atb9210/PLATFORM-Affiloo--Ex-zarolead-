<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Order as Order;
use App\Http\Resources\api\v1\OrderResource;
use App\OrderStatus;
use App\Form;
use App\FormStats;
use App\Landing as Landing;
use App\LandingStats as LandingStats;

class OrderController extends Controller
{
    /**
     * Get all orders list.
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     * @urlParam filter_status int Order's status id
     * @urlParam filter_product int Order's product id
     *
     * @response {
     *  "data": [orders]
     * }
     */
    public function index(Request $request)
    {
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
    public function show($id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('view', $order);

        if ($order->archived) {
            return response([
                "message" => "That order is archived"
            ], 403);
        }

        return new OrderResource($order);
    }

    /**
     * Create a new order.
     *
     * @bodyParam name string required Customer's name
     * @bodyParam phone string required Customer's phone number
     * @bodyParam address string required Customer's address
     * @bodyParam quantity string required Quantity
     * @bodyParam form_id integer required Form's id
     * @bodyParam network_data json required Network Data in json format
     * @bodyParam landing_id integer Landing's id
     *
     * @response 201 {
     *  "message": "Order created",
     *  "data": [order],
     * }
     */
    public function store(Request $request)
    {
        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
            'phone' => 'required|string|min:3',
            'address' => 'required|string|min:3',
            'quantity' => 'required|string|min:1',

            'form_id' => 'required|integer|exists:forms,id',
            'network_data' => 'required|json',

            'landing_id' => 'integer|exists:landings,id',
        ]);

        $form = Form::findOrFail($params['form_id']);
        $network_data = json_decode($params['network_data'], TRUE);
        $network_params = json_decode(json_encode($form->network_params), TRUE);

        $params['user_id'] = $form->user_id;
        $params['network_id'] = $form->network_id;
        $params['product_id'] = $form->product_id;
        $params['order_status_id'] = OrderStatus::NOT_SENT;

        $price = 0.0;
        if (isset($form->quantity['options'][$params['quantity']]['price'])) {
            $price = $form->quantity['options'][$params['quantity']]['price'];
        }
        $commission = 0.0;
        if (isset($form->quantity['options'][$params['quantity']]['commission'])) {
            $commission = $form->quantity['options'][$params['quantity']]['commission'];
        }
        $sub_commission = 0.0;
        if (isset($form->quantity['options'][$params['quantity']]['sub_commission'])) {
            $sub_commission = $form->quantity['options'][$params['quantity']]['sub_commission'];
        }
        $params['price'] = $price;
        $params['commission'] = $commission;
        $params['sub_commission'] = $sub_commission;

        $network_data['price'] = $price;
        $network_data['commission'] = $price;
        $network_data['sub_commission'] = $price;

        // Create order
        $order = Order::create($params);

        // Send order to the network
        $api_call = $order->sendToNetwork($network_data, $network_params);

        if( $order->order_status_id != OrderStatus::NOT_SENT &&
            $order->order_status_id != OrderStatus::DOUBLE &&
            $order->order_status_id != OrderStatus::REFUSED) {

            $today = date('Y-m-d');

            // Increase form leads and commission
            $formStats = FormStats::getObject($form->id, $today);
            $formStats->leads += 1;
            $formStats->total_commission += $order->commission;
            $formStatsInfo = $formStats->info;
            if (!isset($formStatsInfo['total_sub_commission'])) {
                $formStatsInfo['total_sub_commission'] = 0.0;
            }
            $formStatsInfo['total_sub_commission'] += $sub_commission;
            $formStats->info = $formStatsInfo;
            $formStats->save();

            // Landing stats
            if (isset($params['landing_id'])) {
                $landing = Landing::find($params['landing_id']);
                if($landing != null && $landing->form_id == $form->id) {
                    $stats = LandingStats::getObject($form->id, $today);
                    $stats->leads += 1;
                    $stats->total_commission += $order->commission;
                    $statsInfo = $stats->info;
                    if (!isset($statsInfo['total_sub_commission'])) {
                        $statsInfo['total_sub_commission'] = 0.0;
                    }
                    $statsInfo['total_sub_commission'] += $sub_commission;
                    $stats->info = $statsInfo;
                    $stats->save();
                }
            }

            // Total
            $form->leads += 1;
            $form->total_commission += $order->commission;
            $formInfo = $form->info;
            if (!isset($formInfo['total_sub_commission'])) {
                $formInfo['total_sub_commission'] = 0.0;
            }
            $formInfo['total_sub_commission'] += $sub_commission;
            $form->info = $formInfo;
            $form->save();
        }

        return response([
            "message" => "Order created",
            "data" => new OrderResource($order),
            "api_call" => $api_call,
        ], 201);
    }

    /**
     * Update the order's fields.
     *
     * @urlParam id int required Order's id
     *
     * @bodyParam name string Customer's name
     * @bodyParam phone string Customer's phone number
     * @bodyParam address string Customer's address
     * @bodyParam product_id int Order's product id
     * @bodyParam quantity string Quantity
     * @bodyParam commission float Commission
     * @bodyParam sub_commission float Sub commission
     * @bodyParam network_id int Order's network id
     *
     * @response {
     *  "data": order
     * }
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $this->authorize('update', $order);

        // Params validation
        $params = $request->validate([
            'order_status_id' => 'nullable|int|min:1|exists:order_statuses,id',
            'name' => 'nullable|string|min:3',
            'phone' => 'nullable|string|min:3',
            'address' => 'nullable|string|min:3',
            'product_id' => 'nullable|integer|min:1|exists:products,id',
            'quantity' => 'nullable|string|min:1',
            'commission' => 'nullable|numeric|min:1',
            'sub_commission' => 'nullable|numeric|min:1',
            'network_id' => 'nullable|integer|min:1|exists:networks,id',
        ]);

        // Update orders information
        $order->update($params);

        return new OrderResource($order);
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
    public function destroy($id)
    {
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
