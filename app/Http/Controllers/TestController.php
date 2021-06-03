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
        $process = Ssh::create(env('SFTP_USER'), env('SFTP_HOST'))
            ->executeAsync(
                'python3 ' . env('ZAROCLONER_PATH') .
                ' https://it.wikipedia.org/wiki/Pagina_principale ' .
                env('ZAROCLONER_PATH') . '/projects/wikipedia'
            );
        if ($process->isSuccessful()) {
            echo "OK";
        } else {
            echo "NO";
        }
        //try {
        //    $process = new Process([
        //        'python3',
        //        env('ZAROCLONER_PATH') . '/zarocloner.py',
        //        'https://it.wikipedia.org/wiki/Pagina_principale',
        //        env('ZAROCLONER_PATH') . '/projects/wikipedia',
        //    ]);
        //    //$process->run();
        //    $process->start();
        //    dd($process->getOutput());
        //} catch (Exception $e) {
        //    dd($e);
        //}
        ////$config = [
        ////    'id' => env('SERVERPILOT_CLIENT_ID'),
        ////    'key' => env('SERVERPILOT_API_KEY'),
        ////];
        ////$sp = new \ServerPilot($config);
        //////dd($sp->sysuser_list());
        ////$app = $sp->app_create(
        ////    'test-to-delete2',
        ////    env('SERVERPILOT_LANDINGS_SYSUSER_ID'),
        ////    'php7.4',
        ////    ['test-to-delete2.com'],
        ////    []
        ////);
        ////dd($app->data->id);
        ////$app_id = 'Fm8pKxjL2ZO9J37I';
        ////dd($sp->ssl_auto($app_id));
        ////dd($sp->ssl_force($app_id, true));
        ////$key     = new \Cloudflare\API\Auth\APIKey(env('CLOUDFLARE_USER'), env('CLOUDFLARE_API_KEY'));
        ////$adapter = new \Cloudflare\API\Adapter\Guzzle($key);
        ////$user    = new \Cloudflare\API\Endpoints\User($adapter);
        ////$userId = $user->getUserID();
        ////exit();
        ////$accounts = new \Cloudflare\API\Endpoints\Accounts($adapter);
        ////$accounts->getDomains($userId);
        ////dd($accounts->getDomains(env('CLOUDFLARE_ACCOUNT_ID')));
        ////$zones = new \Cloudflare\API\Endpoints\Zones($adapter);
        ////dd($zones->addZone('example.club'));
        //$headers = [
        //    'Content-Type: application/json',
        //    'Authorization: Bearer ' . env('CLOUDFLARE_API_KEY'),
        //    //'X-Auth-Key: ' . env('CLOUDFLARE_API_KEY'),
        //    //'X-Auth-Email: ' . env('CLOUDFLARE_USER'),
        //];
        //$url = 'https://api.cloudflare.com/client/v4/zones';
        //$params = [
        //    'name' => 'speranza.com',
        //    'account' => [
        //        'id' => env('CLOUDFLARE_ACCOUNT_ID'),
        //    ],
        //];
        //$ch = curl_init();
        //$id = 'ec5b83f08908ccc652ef667af9c67abd';
        //curl_setopt($ch, CURLOPT_URL, $url . '/' . $id . '/dns_records');
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        ////curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        ////curl_setopt($ch, CURLOPT_POST, 1);
        ////curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        //$response = curl_exec($ch);
        //curl_close($ch);
        //$response = json_decode($response, true);
        //dd($response);
        //if (isset($response['success']) && $response['success']) {
        //    $zones = $response['result'];
        //}
        //dd($zones);
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
