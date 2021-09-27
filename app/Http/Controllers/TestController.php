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
        $appSecret = '766d936bd8980c34b4947960e479a9be';
        $appId = '1031757410984629';
        $fb = new \Facebook\Facebook([
            'app_id' => $appId,
            'app_secret' => $appSecret,
        ]);
        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email'];
        $loginUrl = $helper->getLoginUrl('http://zarolead.localhost/test2', $permissions);
        echo '<a href=' . $loginUrl . '>' . $loginUrl . '</a>';
        //$response = $fb->get('/me');
        exit();

        $accessToken = 'EAAOqYL354rUBANdBem5nAir5Ed8zK5IzoFiJ7mBwIcyoFe80aPl79wYVoB93JL0t4np8GQNsrS5YRhFHc6cV5ibXotoGxXZAClzq85Knv06aJHzaIt9XOOnH8o4bZAErmbxHRSWKHLoJtTNhKn2sP0V8aRSg3oZA6dYM1HctPcw8n35pVdsSEZAsLxikHX4ZD';
        $adAccountId = 'act_2742408822725474';
        $appSecret = '766d936bd8980c34b4947960e479a9be';
        $appId = '1031757410984629';

        $fb = \FacebookAds\Api::init($appId, $appSecret, $accessToken);
        $fields = [
            'reach',
            'frequency',
            'impressions',
            'spend',
        ];
        $params = array(
            'time_range' => array('since' => '2021-08-27','until' => '2021-09-26'),
            //'filtering' => array(array('field' => 'delivery_info','operator' => 'IN','value' => array('active','scheduled','pending_review','recently_rejected','rejected','inactive','archived','permanently_deleted','not_delivering','not_published','rejected','completed','recently_completed'))),
            'level' => 'campaign',
            'breakdowns' => array(),
        );
        echo json_encode((new \FacebookAds\Object\AdAccount($adAccountId))->getInsights(
            $fields,
            $params
        )->getResponse()->getContent(), JSON_PRETTY_PRINT);


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
