<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\FormStats;

class TestController extends Controller
{
    public function test()
    {
        return view('test');
        $s = [];
        $s[] = 'host::port::user::password';
        $s[] = 'ciao::2';
        $s[] = '127.0.0.1::80::user::password';
        $s[] = '127.0.0.1::80::::';
        $s[] = '127.0.0.1::80';
        
        $regex = "/^[a-zA-Z0-9-.]+::[0-9]+::[a-zA-Z0-9-.]+::.+$/";
        foreach ($s as $value) {
            echo $value . " => " . preg_match($regex, $value) . "<br>";
        }
        exit();
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
