<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    public function test()
    {
        $order = Order::findOrFail(158);
        $form = Form::findOrFail(1);
        /*
        echo "<pre>".var_export($form->network_params, true)."</pre>";
        exit();
        */
        $network_params = json_decode(json_encode($form->network_params), TRUE);
        $network_data = [
            'name' => explode(" ", $order->name)[0],
            'surname' => explode(" ", $order->name)[1],
            'phone' => $order->phone,
            'address' => $order->address,
            'quantity' => $order->quantity,
            'price' => 99.99,
        ];
        $network_data = json_decode(json_encode($network_data), TRUE);
        $res = $order->sendToNetwork($network_data, $network_params);
        echo "<pre>" . var_export($res, true) . "</pre>";
        exit();
    }

    public function post(Request $request)
    {
        echo "<pre>" . var_export($request, true) . "</pre>";
        exit();
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
