<?php

namespace App\Http\Controllers\api\fake;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\third\LogisticsOrder;

class NetworkController extends Controller
{
    public function worldfilia(Request $request)
    {
        return response([
            'status' => 'success',
        ]);
    }

    public function amsped(Request $request)
    {
        return response([
            'error' => false,
        ]);
    }

    public function affiliationPark(Request $request)
    {
        return response([
            'status' => true,
            'response' => 'Order added',
        ]);
    }

    public function zarolead(Request $request)
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
        ]);
        if ($validator->fails()) {
            return response([
                'message' => 'The given data was invalid.',
                'errors' => $validator->messages(),
            ], 422);
        }

        $params = $request->query();
        $logisticsUrl = 'http://zarolead.localhost/api/fake/logistic/momoka';

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

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $logisticsUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
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
                'status' => 'success',
            ]);
        } else {
            return response([
                'status' => 'error',
            ], 500);
        }
    }
}
