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
        //$telegram = new \Longman\TelegramBot\Telegram(env('TELEGRAM_BOT_API_KEY'), env('TELEGRAM_BOT_USERNAME'));
        //dd($telegram->handleGetUpdates());
        $url = 'https://api.telegram.org/bot' .  env('TELEGRAM_BOT_API_KEY') . '/getUpdates';
        echo $url;
        exit();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "example.com");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);  
        echo $output;
        exit();

        dd($output);
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
