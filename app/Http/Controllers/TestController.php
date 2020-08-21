<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Landing;

class TestController extends Controller
{
    public function test()
    {
        include('http://www.google.com/');
        exit();
        /*
        $p = public_path("robots.txt");
        $p = "test/index.php";
        var_dump(Storage::disk('flows')->url($p));
        $pathname = 'bau/ciao/yaoming/miao/ciao.php';
        $path = pathinfo($pathname);
        $dir = $path['dirname'];
        var_dump($dir);
        */
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
