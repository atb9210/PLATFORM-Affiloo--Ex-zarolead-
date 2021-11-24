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
    public function test()
    {
        $apiKey = env('STRIPE_API_SECRET');
        $sessionId = 's_test_a1apgXW2I61KXYjfpkorRMpn3mtuuazwuMYQisy45Wmg6kTFXRdFMIh7XX';
        $stripe = new \Stripe\StripeClient($apiKey);
        dd($stripe->checkout->sessions->retrieve($sessionId, []));
        /*
        $apiKey = env('STRIPE_API_SECRET');
        $sessionId = 'cs_test_a1apgXW2I61KXYjfpkorRMpn3mtuuazwuMYQisy45Wmg6kTFXRdFMIh7XX';
        $stripe = new \Stripe\StripeClient($apiKey);
        dd($stripe->checkout->sessions->retrieve($sessionId, []));
        $session = $stripe->checkout->sessions->retrieve($sessionId, []);
        dd($session);
        exit();
        */

        $apiKey = env('STRIPE_API_SECRET');
        \Stripe\Stripe::setApiKey($apiKey);
        $session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'T-Shirt',
                        ],
                        'unit_amount' => 1999,
                    ],
                    'quantity' => 2,
                ],
            ],
            'mode' => 'payment',
            'success_url' => 'https://zarolead.localhost/test2?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'https://bing.com',
        ]);
        return redirect($session->url);
        exit();

        $elogy = new \Driverfury\Elogy\Elogy(
            env('ELOGY_API_TOKEN'),
            env('ELOGY_EMAIL'),
            env('ELOGY_PASSWORD'));
        $orderId = '2d171010-4c7a-11ec-96a3-df3a0a5f3bff';
        $objDateTime = new \DateTime('NOW');
        dd($elogy->getOrder($orderId));
        dd($elogy->updateOrder(
            $orderId,
            [
                /*
                'customer' => [
                    'first_name' => 'testFirstName',
                    'last_name' => 'testLastName',
                    'phone' => '+(39)1234567890',
                    'mail' => 'test@zarolead.com',
                ],
                */
                'completed_at' => $objDateTime->format(\DateTime::ISO8601),
                'transform_to_order' => 1,
            ],
        ));
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
