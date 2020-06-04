<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Order;

class TestController extends Controller {
    public function test() {
        $order = Order::findOrFail(1);
        $res = $order->sendToNetwork();
        echo "<pre>" . var_export($res, true) . "</pre>";
        exit();
    }

    public function post(Request $request) {
        echo "<pre>" . var_export($request, true) . "</pre>";
        exit();
    }
}
