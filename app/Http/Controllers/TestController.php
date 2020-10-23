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
        $formStats = FormStats::findOrFail(1);
        $c = $formStats->custom_params_stats;
        if ($c === null) {
            echo "CIAONE";
        }
        echo "<pre>".var_export($c, true)."</pre>";
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
