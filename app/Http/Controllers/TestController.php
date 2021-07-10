<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Spatie\Ssh\Ssh;

use App\FormStats;
use App\User;

class TestController extends Controller
{
    private function testFraudFilter()
    {
        $tests = [
            [
                'id' => 'a55vk',
                'key' => '9fc7f9a2-56b3-4d65-92a2-3ab2d244f558',
            ],
            [
                'id' => '5puug',
                'key' => '1e6b4d4d-cfa8-46ee-8c32-81cb8aced48d',
            ],
            [
                'id' => 'c9pjd',
                'key' => '4e13e802-3fcf-4f43-8a42-ab278715e32e',
            ],
            [
                'id' => 'dbd70',
                'key' => '94cc6baa-3ae0-4da5-a295-330ea8667d2e',
            ],
        ];

        $results = [
            'yes' => '<span style="color:green;font-weight:bold;">OK</span>',
            'no'  => '<span style="color:red;font-weight:bold;">NO</span>',
        ];

        $ff = new \Driverfury\FraudFilter\FraudFilter();

        $output = '<pre>';
        foreach ($tests as $test) {
            $response = $ff->check($test['id'], $test['key']);
            $output .= '[' . $results[($response ? 'yes' : 'no')] . '] campaign id: ' . $test['id'] . ' => ' . ($response ? 'MONEY' : 'SAFE') . '<br>';
        }
        $output .= '<pre>';
        echo $output;
    }

    private function testCloudflare()
    {
        $cloudflare = new \Driverfury\Cloudflare\Cloudflare(env('CLOUDFLARE_API_KEY'));
        $zoneId = '5bc44efbb4f17dc137fa7c114aa96971';
        dd($cloudflare->createDNSRecord($zoneId, 'TXT', '@', 'mycontent'));
    }

    private function testWorldfilia()
    {
        $token = env('WORLDFILIA_API_TOKEN');
        $worldfilia = new \Driverfury\Worldfilia\Worldfilia($token);
        $productJson = $worldfilia->getProductJson(1611, env('WORLDFILIA_SOURCE_ID'));
        echo "<pre>".var_export($productJson,true)."</pre>";
    }

    public function test()
    {
        $ch = curl_init();

        $params = [
            'app_id' => '6922555156701642753',
            'secret' => '49aad18291b3c0fd96441b863adbc02630b5518c',
            'auth_code' => '45258b07f354d71c3ceb50f22184aebdacc68ee5',
        ];
        $url = 'https://ads.tiktok.com/open_api/v1.2/oauth2/access_token/';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        /*
        $app_id = '6922555156701642753';
        $secret = '49aad18291b3c0fd96441b863adbc02630b5518c';
        $access_token = 'b642829183a89ee4150942db354b0c67cf4539c9';
        $params = [
            'app_id' => $app_id,
            'secret' => $secret,
            'access_token' => $access_token,
        ];
        $url = 'https://ads.tiktok.com/open_api/v1.2/advertiser/info/?'.http_build_query($params);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Access-Token: ' . $access_token,
        ]);
         */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);

        echo '<pre>'.var_export($server_output, true).'</pre>';
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
