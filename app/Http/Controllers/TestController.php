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
        $arr = [
            1 => 'ciao',
            33 => 'asd',
        ];
        var_dump(isset($arr[0]));
        var_dump(isset($arr[1]));
        exit();

        $order = \App\Order::findOrFail(1);
        $form = \App\Form::findOrFail(1);
        $landing = \App\Landing::findOrFail(1);
        $commission = 10.0;
        $sub_commission = 4.0;
        $order->commission = $commission;
        $order->save();
        $order->updateStats(
            $commission, $sub_commission,
            $form, [],
            $landing);
        exit();

        $bodyParams = [
            'uid' => 'a2a84d6c-53f0-4a43-ad58-abf6b9367af0',
            'offer' => '514',
            'lp' => '1598',
            'name' => 'Test test',
            'tel' => '12345678901',
            'street-address' => 'Via dei test 2',
            'ip' => '',
            'ua' => '',
        ];
        $url = 'https://new-tech.co/forms/api/';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($bodyParams));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $server_output = curl_exec($ch);
        curl_close($ch);
        var_dump($server_output);
        exit();

        $bodyParams = [
            'firstname' => 'Test',
            'lastname' => 'Test',
            'phone' => '0810000000',
            'email' => 'me@test.com',
            'address' => 'Via dei Test, 1',
            'city' => 'Roma',
            'zipcode' => '00000',
            'country' => 'ES',
            'affiliate_id' => '24',
            'offer_id' => '531',
            'lid' => '1038',
            'quantity' => '1',
            'amount' => '129',
            'apikey' => 'aeGWVLPi8PdExguIt6pHtjr2PCGT2d9VsFxVk1Ac',
            'sub_id' => 'abcde123',
            'notes' => 'This is a note'
        ];
        $url = 'https://app.limitl3ssnetwork.com/api/affiliate/leads/create';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($bodyParams));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $server_output = curl_exec($ch);
        curl_close($ch);
        var_dump($server_output);
        exit();

        $reports = [
            'metrics' => [
                'total_form_value',
                'spend',
                'conversion',
                'cost_per_conversion',
                'conversion_rate',
                'cpm',
                'impressions'
            ],
            'start_date' => '2021-09-01',
            'end_date'   => '2021-09-30',
        ];
        $parentId = '1709746633115650';
        $advertiserId = '6937588729175392257';

        $tt = new \Driverfury\TikTok\TikTok(env('TIKTOK_API_KEY'));
        $adgroups = $tt->getAdgroups($advertiserId, $parentId, $reports);
        var_dump($adgroups);

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
