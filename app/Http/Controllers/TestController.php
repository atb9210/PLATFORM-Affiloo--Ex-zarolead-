<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\User;
use App\AdvertiserReport;
use App\TrafficSource;

class TestController extends Controller
{
    public function test()
    {
        $t = TrafficSource::find(1);
        echo var_export($t->getUrlParams(), true);
        exit();
        //phpinfo();
        //exit();
        //$url = 'http://zarolead.localhost/test';
        $url = 'http://zarolead.localhost/api/fake/network/worldfilia';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        $bodyParams = [
            'Ciao' => '111',
            'Ciaone' => '222',
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($bodyParams));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $server_output = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //var_dump([
        //    gethostbyname($url),
        //    gethostbyname('zarolead.localhost'),
        //    gethostbyname(env('APP_URL')),
        //]);
        //exit();
        echo var_export($http_code, true);
        echo var_export(curl_error($ch), true);
        curl_close($ch);
        echo var_export($server_output, true);
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
