<?php

namespace App\Http\Controllers\api\fake;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $logisticUrl = 'http://zarolead.localhost/api/fake/logistic/momoka';

        $bodyParams = [
            'user_id'        => '11665252',//'8748434',
            'org_id'         => '1',
            'advertiser'     => '0030',
            'pipeline_id'    => '1',
            'order_id'       => '[IL TUO RIFERIMENTO ORDINE SE PRESENTE]',
            'email'          => 'debora.carofiglio@gmail.com',
            'phone'          => '3890054330',
            'address'        => 'Via G. Di Vittorio 27',
            'city'           => 'Città Sant’Angelo',
            'zipcode'        => '65013',
            'state'          => 'PE',
            'country'        => 'ITA',
            'product'        => 'Quantità + SKU + nome prodotto',
            'quantity'       => '1',
            'payment'        => 'Alla consegna',
            'aff_id'         => '[nome o numero affiliato]',
            'contactname'    => '[nome citofono]',
            'fullname'       => '[nome e cognome]',
            'weight'         => '[peso degli articoli]',
            'productCode'    => '[SKU]',
            'transaction_id' => '[numero transazione della conversione]',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $logisticUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($bodyParams));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $server_output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $response = json_decode($server_output, true);

        if ($httpCode == 200 && isset($response['success']) && $response['success'] == true) {
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
