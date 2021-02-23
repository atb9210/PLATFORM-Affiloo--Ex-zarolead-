<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\FormStats;
use App\User;

class TestController extends Controller
{
    public function test()
    {
        $bodyParams = [
            'offer_id' => 28,
            'nome' => 'test',
            'cognome' => 'test',
            'email' => 'test@example.com',
            'telefono' => '123456789',
            'qta' => '1',
        ];

        $physom_token = '7|tIuiQ3C6GuFekt46mlAZbwrpx9KXLYYFO0xVSXSqvC2HTrYh9KNRZyCyeltBvaurg6AtWAY24OzXxYCD';
        $logisticsUrl = 'https://fynapsyhost.com/api/publisher/orders/send';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $logisticsUrl . '?' . http_build_query($bodyParams));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $physom_token,
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $server_output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $response = json_decode($server_output, true);

        echo "http code: " . $httpCode . "<br>";
        print_r($response);
        exit();
    }

    public function post(Request $request)
    {
        return response([
            'YO' => 'man'
        ]);
    }

    public function hash(Request $request)
    {
        $result = '';
        $psw = '';
        if($request->input('psw') != '') {
            $psw = $request->input('psw');
            $result = Hash::make($psw);
        }
        return view('psw-maker')->with([
            'psw' => $psw,
            'result' => $result,
        ]);
    }
}
