<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\third\LogisticsOrder;
use App\Http\Resources\api\third\LogisticsOrderResource;

class NetworkController extends Controller
{
    /**
     * Get all orders.
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     *
     * @response {
     *  "data": [orders],
     * }
     */
    public function index(Request $request)
    {
        //$this->authorize('viewAny', 'App\Tag');
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
        ]);

        $orders = LogisticsOrder::query();
        if (isset($params['filter_date_from'])) {
            $orders = $orders->where('created_at', '>=', $params['filter_date_from'] . " 00:00:00");
        }
        if (isset($params['filter_date_to'])) {
            $orders = $orders->where('created_at', '<=', $params['filter_date_to'] . " 23:59:59");
        }
        $orders = $orders->orderBy('created_at', 'desc');

        return LogisticsOrderResource::collection($orders->get());
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
        $order = LogisticsOrder::findOrFail($id);
        //$this->authorize('view', $tag);

        return new LogisticsOrderResource($order);
    }

    /**
     * Store order in internal network
     *
     * @bodyParam order_id required int Order id
     * @bodyParam email required string Customer's email
     * @bodyParam phone required string Customer's phone number
     * @bodyParam address required stirng Customer's address
     * @bodyParam city requierd string Customer's city
     * @bodyParam zipcode requierd string Customer's zipcode
     * @bodyParam state requierd string Customer's state
     * @bodyParam country requierd string Customer's country
     * @bodyParam quantity requierd string Product's quantity
     * @bodyParam price requierd float Order's price (amount)
     * @bodyParam name requierd string Customer's name
     * @bodyParam weight requierd string Customer's weight
     * @bodyParam sku requierd string Product's sku code
     * @bodyParam transaction_id requierd string Transaction id
     *
     * @response {
     *  "status": "success",
     *  "data": order,
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'order_id'       => 'required|integer|exists:orders,id',
            'email'          => 'required|email',
            'phone'          => 'required|string|min:7',
            'address'        => 'required|string|min:4',
            'city'           => 'required|string|min:2',
            'zipcode'        => 'required|string|min:2',
            'state'          => 'required|string|min:2',
            'country'        => 'required|string|min:2',
            'quantity'       => 'required|string',
            'price'          => 'required|numeric|min:0',
            'name'           => 'required|string',
            'weight'         => 'required|string',
            'product_name'   => 'required|string',
            'sku'            => 'required|string',
            'transaction_id' => 'nullable|string',
            'cross_sell'     => 'json',
        ]);
        if ($validator->fails()) {
            return response([
                'message' => 'The given data was invalid.',
                'errors' => $validator->messages(),
            ], 422);
        }

        $params = $request->query();

        if (isset($params['cross_sell'])) {
            $params['cross_sell'] = json_decode($params['cross_sell'], true);
        }

        $logisticsUrl = 'http://api.momokacorp.com/send_order.php';

        $bodyParams = [
            'user_id'        => '11665252',
            'org_id'         => '1',
            'advertiser'     => '0030',
            'pipeline_id'    => '1',
            'order_id'       => $params['order_id'],
            'email'          => $params['email'],
            'phone'          => $params['phone'],
            'address'        => $params['address'],
            'city'           => $params['city'],
            'zipcode'        => $params['zipcode'],
            'state'          => $params['state'],
            'country'        => $params['country'],
            'product'        => $params['quantity'] . ' ' . $params['sku'] . ' ' . $params['product_name'],
            'quantity'       => $params['quantity'],
            'payment'        => 'Alla consegna',
            'amount'         => $params['price'],
            'aff_id'         => '2',
            'contactname'    => $params['name'],
            'fullname'       => $params['name'],
            'weight'         => $params['weight'],
            'productCode'    => $params['sku'],
            'transaction_id' => $params['transaction_id'],
        ];

        // Change product code if cross sell is enabled (add another sku)
        if (isset($params['cross_sell']) && isset($params['cross_sell']['enabled']) && $params['cross_sell']['enabled']) {
            if (isset($params['cross_sell']['sku'])) {
                $bodyParams['productCode'] .= ', ' . $params['cross_sell']['sku'];
            }
        }

        // TODO: Change weight if cross sell is enabled?

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $logisticsUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($bodyParams));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $server_output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $response = json_decode($server_output, true);

        if ($httpCode == 200 && isset($response['success']) && $response['success'] == true) {
            $info = $bodyParams;
            $order = LogisticsOrder::create([
                'logistics_system_id' => 1,
                'order_id' => $params['order_id'],
                'info' => $info,
            ]);
            return response([
                'order' => $order,
                'status' => 'success',
            ]);
        } else {
            return response([
                'status' => 'error',
            ], 500);
        }
    }
}
