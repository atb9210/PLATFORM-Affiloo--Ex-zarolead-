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
        $carts = \App\third\EcommerceCart::all();
        foreach ($carts as $cart) {
            $tmp = $cart->phone;
            $cart->phone = $cart->email;
            $cart->email = $tmp;
            $cart->save();
        }
        exit();

        $elogy = new \Driverfury\Elogy\Elogy(
            env('ELOGY_API_TOKEN'),
            env('ELOGY_EMAIL'),
            env('ELOGY_PASSWORD'));
        exit();

        $elogy = new \Driverfury\Elogy\Elogy(env('ELOGY_API_TOKEN'));
        //dd($elogy->getOrders());
        $elogy->createOrder([
            'order_name' => 'test',
            'order_number' => 'test',
            'country' => 'IT',
            'total_price' => '123.45',
            'sub_total_price' => '113.45',
            'shipping_price' => '10',
            'total_tax' => '0.0',
            'tax_included' => 1,
            'gateway' => 'cashondelivery',
            'currency' => 'EUR',
            'customer_first_name' => 'Test',
            'customer_last_name' => 'Test',
            'shipping_first_name' => 'Test',
            'shipping_last_name' => 'Test',
            'shipping_address1' => 'Via dei Test, 1',
            'shipping_address2' => '',
            'shipping_city' => 'Test',
            'shipping_zip' => '12345',
            'shipping_province_code' => 'RM',
            'shipping_country_code' => 'IT',
            'shipping_phone' => '1234567890',
            'shipping_email' => 'test@zarolead.com',
            'shipping_notes' => '',
            'created_at' => date('dd/mm/YYYY h:i'),
            'completed_at' => date('dd/mm/YYYY h:i'),
            'products' => [
                [
                    'sku' => 'test',
                    'quantity' => 1,
                    'subtotal' => '113.45',
                ],
            ],
            'params' => [],
        ]);
        exit();
    }

    public function test2(Request $request)
    {
        dd($request->all());
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
