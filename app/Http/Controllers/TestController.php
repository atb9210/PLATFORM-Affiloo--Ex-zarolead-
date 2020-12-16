<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\FormStats;

class TestController extends Controller
{
    public function test()
    {
        $context = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];
        Log::channel('orders')->info('this ma test', $context);
        $c1 = 'ciao';
        $c2 = 30;
        echo "<pre>".var_export(json_decode($c1, true), true)."</pre>";
        echo "<pre>".var_export(json_decode($c2, true), true)."</pre>";
        exit();

        $param = [
            'phone1' => '+(39)388577077', // 9 digits instead of 10
            'phone2' => '+(393888577077', // incorrect
            'phone3' => '+393888577077', // incorrect
            'phone4' => '+(1)1388577077', // correct
            'phone5' => '+(319)38857707777', // correct
            'phone6' => '+(39)3885770771', // correct
        ];
        $validator = Validator::make($param, [
            'phone1' => 'required|string|min:10|regex:/^[+]{1}[(]{1}[0-9]{1,4}[)]{1}[0-9]{10,}$/',
            'phone2' => 'required|string|min:10|regex:/^[+]{1}[(]{1}[0-9]{1,4}[)]{1}[0-9]{10,}$/',
            'phone3' => 'required|string|min:10|regex:/^[+]{1}[(]{1}[0-9]{1,4}[)]{1}[0-9]{10,}$/',
            'phone4' => 'required|string|min:10|regex:/^[+]{1}[(]{1}[0-9]{1,4}[)]{1}[0-9]{10,}$/',
            'phone5' => 'required|string|min:10|regex:/^[+]{1}[(]{1}[0-9]{1,4}[)]{1}[0-9]{10,}$/',
            'phone6' => 'required|string|min:10|regex:/^[+]{1}[(]{1}[0-9]{1,4}[)]{1}[0-9]{10,}$/',
        ]);
        if ($validator->fails()) {
            echo "<pre>".var_export($validator->messages(), true)."</pre>";
        } else {
            echo "YES";
        }
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
