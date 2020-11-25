<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Order as Order;
use App\Http\Resources\api\v1\OrderResource;
use App\OrderStatus;
use App\Form;
use App\FormStats;
use App\Landing as Landing;
use App\LandingStats as LandingStats;
use App\UserReport as UserReport;
use App\User as User;
use App\AdvertiserReport as AdvertiserReport;

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
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
            'filter_status' => 'integer|min:0',
            'filter_product' => 'integer|exists:products,id',
        ]);

        // Query orders (applying filters)
        $orders = Order::query();
        if (!Auth::user()->can('viewAny', 'App\Order')) {
            if (Auth::user()->hasRole('advertiser')) {
                $orders = $orders->join('products', 'products.id', '=', 'orders.product_id')
                    ->where('products.advertiser_id', Auth::user()->id);
            } else {
                $orders = $orders->where('user_id', Auth::user()->id);
            }
        }
        if (isset($params['filter_date_from'])) {
            $orders = $orders->where('orders.created_at', '>=', $params['filter_date_from'] . " 00:00:00");
        }
        if (isset($params['filter_date_to'])) {
            $orders = $orders->where('orders.created_at', '<=', $params['filter_date_to'] . " 23:59:59");
        }
        if (isset($params['filter_status'])) {
            $orders = $orders->where('orders.order_status_id', $params['filter_status']);
        }
        if (isset($params['filter_product'])) {
            $orders = $orders->where('orders.product_id', $params['filter_product']);
        }
        $orders = $orders->where('orders.archived', 0);
        $orders = $orders->orderBy('orders.created_at', 'desc');
        $orders = $orders->select('orders.*');

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
     * @bodyParam cross_sell json Cross sell
     * @bodyParam form_id integer required Form's id
     * @bodyParam network_data json required Network Data in json format
     * @bodyParam landing_id integer Landing's id
     * @bodyParam query_params json Query params
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
            'cross_sell' => 'json',

            'form_id' => 'required|integer|exists:forms,id',
            'network_data' => 'required|json',

            'landing_id' => 'integer|exists:landings,id',

            'query_params' => 'json',
        ]);

        // Validate cross_sell JSON
        if (isset($params['cross_sell'])) {
            $params['cross_sell'] = json_decode($params['cross_sell'], TRUE);
            $validator = Validator::make($params['cross_sell'], [
                'enabled' => 'required|boolean',
                'price' => 'required|numeric',
                'commission' => 'required|numeric',
                'sub_commission' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return response([
                    'message' => 'The given data - for the cross_sell JSON param - was invalid.',
                    'errors' => $validator->messages(),
                ], 422);
            }
        }

        $form = Form::findOrFail($params['form_id']);
        $network_data = json_decode($params['network_data'], TRUE);
        $network_params = json_decode(json_encode($form->network_params), TRUE);

        $params['user_id'] = $form->user_id;
        $params['network_id'] = $form->network_id;
        $params['product_id'] = $form->product_id;
        $params['order_status_id'] = OrderStatus::NOT_SENT;

        // Calculate price
        $price = 0.0;
        if (isset($form->quantity['options'][$params['quantity']]['price'])) {
            $price = $form->quantity['options'][$params['quantity']]['price'];
        }
        if (isset($params['cross_sell']) && $params['cross_sell']['enabled']) {
            $price += floatval($params['cross_sell']['price']);
        }

        // Calculate commission
        $commission = 0.0;
        if (isset($form->quantity['options'][$params['quantity']]['commission'])) {
            $commission = $form->quantity['options'][$params['quantity']]['commission'];
        }
        if (isset($params['cross_sell']) && $params['cross_sell']['enabled']) {
            $price += floatval($params['cross_sell']['commission']);
        }

        // Calculate sub_commission
        $sub_commission = 0.0;
        if (isset($form->quantity['options'][$params['quantity']]['sub_commission'])) {
            $sub_commission = $form->quantity['options'][$params['quantity']]['sub_commission'];
        }
        if (isset($params['cross_sell']) && $params['cross_sell']['enabled']) {
            $price += floatval($params['cross_sell']['sub_commission']);
        }

        $params['price'] = $price;
        $params['commission'] = $commission;
        $params['sub_commission'] = $sub_commission;
        $params['info'] = [
            'price' => $price,
            'commission' => $commission,
            'sub_commission' => $sub_commission,
        ];

        $network_data['price'] = $price;
        $network_data['commission'] = $price;
        $network_data['sub_commission'] = $price;
        $network_data['ip'] = $request->ip();

        // Create order
        $order = Order::create($params);

        // Send order to the network
        $network = $order->network;
        $api_call = $order->sendToNetwork($network_data, $network_params, $network);

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
            if (isset($params['query_params'])) {
                $query_params = json_decode($params['query_params'], true);
                $custom_params_stats = $formStats->custom_params_stats;
                if ($custom_params_stats === null) {
                    $custom_params_stats = [];
                }
                foreach ($query_params as $k => $v) {
                    if (!isset($custom_params_stats[$k])) {
                        $custom_params_stats[$k] = [];
                    }
                    if (!isset($custom_params_stats[$k][$v])) {
                        $custom_params_stats[$k][$v] = [
                            'visits' => 0,
                            'leads' => 0,
                            'total_commission' => 0.0,
                        ];
                    }
                    $custom_params_stats[$k][$v]['leads'] += 1;
                    $custom_params_stats[$k][$v]['total_commission'] += $order->commission;
                }
                $formStats->custom_params_stats = $custom_params_stats;
            }
            $formStats->save();

            // Landing stats
            if (isset($params['landing_id'])) {
                $landing = Landing::find($params['landing_id']);
                if($landing != null && $landing->form_id == $form->id) {
                    $stats = LandingStats::getObject($landing->id, $today);
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

            // Update user's report stats
            $report = UserReport::getObject($form->user->id, $today);
            $report->leads += 1;
            $report->commission += $order->commission;
            $report->profit = $report->commission - ($report->cost + $report->extra_cost);
            $report->save();

            // Update advertiser's report stats
            $timestamp = strtotime($today);
            $month = intval(date('m', $timestamp));
            $year = intval(date('Y', $timestamp));
            $advertiser = User::findOrFail($form->product->advertiser_id);
            $advReport = AdvertiserReport::getObject($advertiser, $month, $year);
            $advReport->leads += 1;
            $advReport->commission += $order->commission;
            $advReport->profit = $advReport->commission - $advReport->cost;
            $advReport->save();
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
     * @bodyParam address string Customer's email
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

        // Params validation
        $params = $request->validate([
            'order_status_id' => 'int|min:1|exists:order_statuses,id',
            'name' => 'string|min:3',
            'phone' => 'string|min:3',
            'address' => 'string|min:3',
            'email' => 'email|min:3',
            'product_id' => 'integer|min:1|exists:products,id',
            'quantity' => 'string|min:1',
            'commission' => 'numeric|min:1',
            'sub_commission' => 'numeric|min:1',
            'network_id' => 'integer|min:1|exists:networks,id',
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

    /**
     * Double order recovery (pick network data from a double order and send
     * them to another network using network_api's values of a specified form).
     *
     * @bodyParam form_id integer required Form's id
     * @bodyParam network_data json required Network Data in json format
     *
     * @response 200 {
     *  "message": "Double order recovered",
     * }
     */
    public function doubleRecovery(Request $request)
    {
        // Params validation
        $params = $request->validate([
            'form_id' => 'required|integer|min:1|exists:forms,id',
            'network_data' => 'required|json',
        ]);

        $form = Form::findOrFail($params['form_id']);
        $network_data = json_decode($params['network_data'], TRUE);
        $network_params = json_decode(json_encode($form->network_params), TRUE);

        $price = 0.0;
        $commission = 0.0;
        $sub_commission = 0.0;
        if (isset($form->quantity['options']) && count($form->quantity['options']) >= 1) {
            $k = '';
            if (isset($network_data['quantity'])) {
                $k = $network_data['quantity'];
            }
            if ($k == '' || !isset($form->quantity['options'][$k])) {
                if (count($form->quantity['options']) >= 1) {
                    $k = key($form->quantity['options']);
                }
            }
            if ($k != '') {
                if (isset($form->quantity['options'][$k]['price'])) {
                    $price = $form->quantity['options'][$k]['price'];
                }
                if (isset($form->quantity['options'][$k]['commission'])) {
                    $commission = $form->quantity['options'][$k]['commission'];
                }
                if (isset($form->quantity['options'][$k]['sub_commission'])) {
                    $sub_commission = $form->quantity['options'][$k]['sub_commission'];
                }
            }
        }

        $network_data['price'] = $price;
        $network_data['commission'] = $price;
        $network_data['sub_commission'] = $price;
        $network_data['ip'] = $request->ip();

        $network = $form->network;
        $api_call = Order::doubleRecovery($network_data, $network_params, $network);

        return response([
            "message" => "Double order recovered",
            "api_call" => $api_call,
        ]);
    }
}
