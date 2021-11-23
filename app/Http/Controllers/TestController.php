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
        $elogy = new \Driverfury\Elogy\Elogy(
            env('ELOGY_API_TOKEN'),
            env('ELOGY_EMAIL'),
            env('ELOGY_PASSWORD'));
        $orderId = '2d171010-4c7a-11ec-96a3-df3a0a5f3bff';
        dd($elogy->getOrder($orderId));
        dd($elogy->updateOrder(
            $orderId,
            [
                'customer' => [
                    'first_name' => 'testFirstName',
                    'last_name' => 'testLastName',
                    'phone' => '+(39)1234567890',
                    'mail' => 'test@zarolead.com',
                ],
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
