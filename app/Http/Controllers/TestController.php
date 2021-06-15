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
        $tests = [
            'https://italiaspecialiofferta.com/ciaoneasdasd',
            'https://italiaspecialiofferta.com/ciaoneasdasd/',
            'https://italiaspecialiofferta.com/ciaoneasdasd/chichi.index.html',
            'http://italiaspecialiofferta.com/index.html',
            'http://italiaspecialiofferta.com',
            'http://italiaspecialiofferta.com/',
        ];

        foreach ($tests as $test) {
            $url = $test;
            $path = parse_url($url, PHP_URL_PATH);
            if (strpos($path, '/') === false) {
                $path = '/' . $path;
            }
            if (strpos($path, '.') !== false) {
                $lastIndex = strrpos($path, '/');
                if ($lastIndex !== false && $lastIndex !== 0) {
                    $path = substr($path, 0, $lastIndex);
                } else if ($lastIndex == 0) {
                    $path = '/';
                }
            }
            if ($path != '/' && $path[strlen($path)-1] != '/') {
                $path .= '/';
            }
            echo $url . ' => ' . $path . '<br>';
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
