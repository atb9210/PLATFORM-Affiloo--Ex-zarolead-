<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\third\FinapsyOrder;
use App\Http\Resources\api\third\FinapsyOrderResource;

class FinapsyController extends Controller
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

        $orders = FinapsyOrder::query();
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
    /*
    public function show($id)
    {
        $order = LogisticsOrder::findOrFail($id);
        //$this->authorize('view', $tag);

        return new LogisticsOrderResource($order);
    }
    */

    /**
     * Store order in internal network
     *
     * @bodyParam order_id required int Order id
     * @bodyParam name required string Customer's name
     * @bodyParam email required string Customer's email
     * @bodyParam phone required string Customer's phone number
     * @bodyParam offer_id requierd string Finapsy's offer id
     * @bodyParam quantity requierd string Product's quantity
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
            'name'           => 'required|string|min:2',
            'email'          => 'required|email',
            'phone'          => 'required|string|min:10|regex:/^[+]{1}[(]{1}[0-9]{1,4}[)]{1}[0-9]{10,}$/',
            'offer_id'       => 'required|string',
            'quantity'       => 'required|string',
        ]);
        if ($validator->fails()) {
            return response([
                'message' => 'The given data was invalid.',
                'errors' => $validator->messages(),
            ], 422);
        }

        $params = $request->query();

        $info = [
            'order_id'       => $params['order_id'],
            'name'           => $params['name'],
            'email'          => $params['email'],
            'phone'          => $params['phone'],
            'offer_id'        => $params['offer_id'],
            'quantity'       => $params['quantity'],
        ];

        $order = FinapsyOrder::create([
            'order_id' => $params['order_id'],
            'info' => $info,
        ]);
        return response([
            'order' => $order,
            'status' => 'success',
        ]);

        /*
        $finapsy_token = '7|tIuiQ3C6GuFekt46mlAZbwrpx9KXLYYFO0xVSXSqvC2HTrYh9KNRZyCyeltBvaurg6AtWAY24OzXxYCD';
        $logisticsUrl = 'https://fynapsyhost.com/api/publisher/orders/index';

        $bodyParams = [
            'offer_id'       => $params['offer_id'],
            'nome'           => $params['name'],
            'cognome'        => $params['name'],
            'email'          => $params['email'],
            'telefono'       => $params['phone'],
            'qta'            => $params['quantity'],
        ];

        $logisticsUrl = 'https://fynapsyhost.com/api/publisher/orders/send';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $logisticsUrl . '?' . http_build_query($bodyParams));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $finapsy_token,
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $server_output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $response = json_decode($server_output, true);

        if ($httpCode == 200) {
            $info = $bodyParams;
            $order = LogisticsOrder::create([
                'logistics_system_id' => 2,
                'order_id' => $params['order_id'],
                'info' => $info,
            ]);

            return response([
                'response' => $response,
                'order' => $order,
                'status' => 'success',
            ]);
        } else {
            return response([
                'response' => $response,
                'status' => 'error',
            ], 500);
        }
        */
    }

    /**
     * Update status of the order in internal network
     *
     * @urlParam id required int Order id
     *
     * @response {
     *  "data": order,
     * }
     */
    /*
    public function updateStatus($id)
    {
        $order = LogisticsOrder::find($id);

        if($order == null) {
            return response([
                'message' => 'The order does not exists.',
            ], 404);
        }

        if($order->order_id == null) {
            return response([
                'response' => 'The order has not the field \'order_id\'.',
                'status' => 'error',
            ], 500);
        }

        $urlParams = [
            'user_id'        => '11665252',
            'org_id'         => '1',
            'advertiser'     => '0030',
            'order_id'       => $order->order_id,
        ];

        $logisticsUrl = 'https://api.momokacorp.com/findByOrder2.php';
        if (env('APP_NETWORK_FAKE_API', false)) {
            $logisticsUrl = 'http://zarolead.localhost/api/fake/logistic/momoka/find-order/' . $id;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $logisticsUrl . '?' . http_build_query($urlParams));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $server_output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $momokaStatusLookupTable = [
            1  => 'Ordinato',
            32 => 'Nessuna Risposta',
            2  => 'Confermato',
            20 => 'Spedire + avanti',
            27 => 'In preparazione',
            29 => 'Pick&Pack',
            4  => 'Spedito',
            5  => 'Giacenza',
            6  => 'Consegnato',
            8  => 'Rientro al mittente',
            7  => 'Annullato',
            21 => 'Doppio',
            22 => 'Test/Fake',
            30 => 'Dati Incompleti',
            23 => 'In sospeso',
            25 => 'Out of stock',
            34 => 'COVID-19',
        ];

        $response = json_decode($server_output, true);
        if ($httpCode == 200 && isset($response['success']) && $response['success'] == true) {
            $status = $response['status'];
            if(!array_key_exists($status, $momokaStatusLookupTable)) {
                return response([
                    'response' => 'Status ' . $status . ' does not exist in lookup table.',
                    'status' => 'error',
                ], 500);
            }

            $order->status = $momokaStatusLookupTable[$status];
            $order->save();

            return response([
                'data' => new LogisticsOrderResource($order),
            ]);
        } else {
            return response([
                'response' => $response,
                'status' => 'error',
            ], 500);
        }
    }
    */
}
