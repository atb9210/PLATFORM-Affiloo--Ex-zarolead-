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
        //$php = 'C:\\xampp\\php\\php.exe';
        $php = 'php';
        $command = $php . ' ..\artisan queue:work --queue=zarocloner --stop-when-empty';
        $commands = explode(' ', $command);
        $process = new Process($commands);
        $process->start();
        while ($process->isRunning()) {
        }
        echo $process->getOutput();
        echo $process->getErrorOutput();
        exit();
        dd($process->getOutput());
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
