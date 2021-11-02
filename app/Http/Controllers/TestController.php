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

    private function decodeCartObj($s)
    {
        $type = 'x';
        $len = 0;
        $val = '';

        if(strlen($s) >= 3)
        {
            $type = $s[0];
            $s = substr($s, 2);

            $i = 0;
            while (is_numeric($s[$i])) {
                $i += 1;
            }
            $len = intval(substr($s, 0, $i));

            if ($type == 's') {
                $s = substr($s, $i + 1);
                $val = substr($s, 0, -1);
                $val = substr($val, 1, -1);
                return $val;
            } else if ($type == 'i') {
                return intval($len);
            } else if ($type == 'd') {
                return floatval($len);
            } else if ($type == 'a') {
                $s = substr($s, $i + 1);
                $val = $s;
                if ($val[strlen($val)-1] == ';') {
                    $val = substr($s, 0, -1);
                }
                $val = substr($val, 1, -1);

                $items = [];
                $start = 0;
                $end = 0;
                $scope = 0;
                while($end < strlen($val)) {
                    if ($val[$end] == '{') {
                        $scope += 1;
                    } else if ($val[$end] == '}') {
                        $scope -= 1;
                        if ($scope == 0) {
                            if ($start > 0) {
                                $start += 1;
                            }
                            $items[] = substr($val, $start, $end - $start + 1);
                            $start = $end;
                        }
                    } else if ($val[$end] == ';' && $scope == 0) {
                        if ($start > 0) {
                            $start += 1;
                        }
                        $items[] = substr($val, $start, $end - $start);
                        $start = $end;
                    }
                    $end += 1;
                }

                $even = true;
                $keys = [];
                $values = [];
                foreach ($items as $item) {
                    if ($item == '') {
                        continue;
                    }
                    $item .= ';';
                    if($even) {
                        $keys[] = $this->decodeCartObj($item);
                        $even = false;
                    } else {
                        $values[] = $this->decodeCartObj($item);
                        $even = true;
                    }
                }

                $arr = [];
                $cnt = min(count($keys), count($values));
                for ($i = 0; $i < $cnt; $i += 1) {
                    $arr[$keys[$i]] = $values[$i];
                }
                return $arr;
            } else {
                die("Invalid");
            }
        }

        return null;
    }

    public function test()
    {
        //$s = 'a:3:{s:7:"country";s:2:"IT";s:4:"city";s:8:"Larciano";s:8:"postcode";s:5:"51036";}';
        //$s = 'a:1:{i:0;s:7:"country";i:1;s:2:"IT";i:2;a:1:{i:0;s:2:"Hi";};};';
        $s = 'a:1:{i:0;a:6:{s:13:"product_title";s:29:"Mini Fresa per Unghie (24€)";s:8:"quantity";i:1;s:10:"product_id";i:2811;s:20:"product_variation_id";s:0:"";s:23:"product_variation_price";d:20.45081967;s:11:"product_tax";d:4.5;}}';
        //$s = 'a:1:{i:0;a:6:{s:13:"product_title";s:29:"Mini Fresa per Unghie (24€)";s:8:"quantity";i:1;s:10:"product_id";i:2811;s:20:"product_variation_id";s:0:"";s:23:"product_variation_price";d:20.45081967;s:11:"product_tax";d:4.5;}}';
        $obj = $this->decodeCartObj($s);
        dd($obj);
        exit();

        $carts = \Illuminate\Support\Facades\DB::connection('woocommerce')
            ->table('wp_261c24b4ca_cartbounty')
            ->get();
        dd($carts);
        exit();

        $woo = new \Automattic\WooCommerce\Client(
            env('WOOCOMMERCE_URL'),
            env('WOOCOMMERCE_API_KEY'),
            env('WOOCOMMERCE_API_SECRET')
        );

        dd($woo->get('store'));
        exit();

        $prodId = '2089';
        ////$prodId = '2418';
        ////$prod = $woo->put('products/' . $prodId, [
        ////    'regular_price' => '88.00',
        ////]);
        ////dd($prod);
        ////$products = $woo->get('products');
        //dd($woo->get('products/' . $products[0]->id . '/variations'));
        //dd($woo->get('products/attributes'));
        //dd($woo->get('products/attributes/2/terms'));
        //
        $prodId = '2089';
        dd($woo->get('products/' . $prodId . '/variations'));
        dd($products);

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
