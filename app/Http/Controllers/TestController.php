<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\FormStats;
use App\User;

class TestController extends Controller
{
    public function test()
    {
        $config = [
            'id' => env('SERVERPILOT_CLIENT_ID'),
            'key' => env('SERVERPILOT_API_KEY'),
        ];
        $sp = new \ServerPilot($config);
        //dd($sp->sysuser_list());
        $app = $sp->app_create(
            'test-to-delete2',
            env('SERVERPILOT_LANDINGS_SYSUSER_ID'),
            'php7.4',
            ['test-to-delete2.com'],
            []
        );
        dd($app->data->id);
        $app_id = 'Fm8pKxjL2ZO9J37I';
        dd($sp->ssl_auto($app_id));
        dd($sp->ssl_force($app_id, true));
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
