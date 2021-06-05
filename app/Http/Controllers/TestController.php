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
        $zones = [];

        $url = 'https://api.cloudflare.com/client/v4/zones';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . env('CLOUDFLARE_API_KEY'),
        ];

        $thereAreOtherZones = true;
        $currentPage = 0;
        while ($thereAreOtherZones) {
            $currentPage += 1;

            $params = [
                'per_page' => 50,
                'page' => $currentPage,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response, true);

            foreach ($response['result'] as $zone) {
                $zones[] = $zone;
            }

            $thereAreOtherZones = (isset($response['result_info']) &&
                isset($response['result_info']['total_pages']) &&
                $response['result_info']['total_pages'] < $currentPage);
        }

        dd($zones);
        exit();
        if (isset($response['success']) && $response['success']) {
            $toReturn = $response['result'];
        } else {
            Log::error('Cannot get zones list on Cloudflare - RESPONSE: ' . json_encode($response));
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
