<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Spatie\Ssh\Ssh;

use App\FormStats;
use App\User;

class TestController extends Controller
{
    public function test()
    {
        $content = Storage::get('app/flows_hotload_boilerplate.php');
        dd($content);
        //$ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, 'http://zarolead.localhost/api/v1/flows/25/hotload');
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //$response = curl_exec($ch);
        //curl_close($ch);
        //$response = json_decode($response, true);
        //echo $response['html'];
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
