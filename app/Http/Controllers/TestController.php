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
        $regex = '/^([A-Za-z0-9_-]+\/*)*$/';

        $params = [
            'test1' => 'prova',
            'test2' => '',
            'test3' => '.',
            'test4' => 'cia/',
            'test5' => './cia/',
            'test6' => './cia/asd',
            'test7' => './cia/asd/',
            'test8' => 'cia/asd/',
            'test9' => 'cia asd all',
        ];

        $validator = Validator::make($params, [
            'test1' => 'regex:' . $regex,
            'test2' => 'regex:' . $regex,
            'test3' => 'regex:' . $regex,
            'test4' => 'regex:' . $regex,
            'test5' => 'regex:' . $regex,
            'test6' => 'regex:' . $regex,
            'test7' => 'regex:' . $regex,
            'test8' => 'regex:' . $regex,
            'test9' => 'regex:' . $regex,
        ]);

        if($validator->fails()){
            dd($validator->messages());
        } else {
            echo "OK";
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
