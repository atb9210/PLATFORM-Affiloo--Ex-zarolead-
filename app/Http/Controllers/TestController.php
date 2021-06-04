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
        $client = new \Namecheap\Api(
            env('NAMECHEAP_USER'),
            env('NAMECHEAP_API_KEY'),
            env('NAMECHEAP_USER'),
            env('NAMECHEAP_CLIENT_IP'),
            'array'
        );
        $client->setCurlOption(CURLOPT_SSL_VERIFYPEER, false); // For local development env (if needed)

        $ncDomains = new  \Namecheap\Domain\Domains($client);
        $ncDomains->enableSandbox(); // Enable sandbox mode for the current instance
        //dd($ncDomains);
        //exit();
        $domainList = $ncDomains->getList(); 
        dd($domainList);
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
