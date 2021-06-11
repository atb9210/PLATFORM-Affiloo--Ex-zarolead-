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
            'https://erboristeriadelbosco.com/fucus/',
            'https://marianaplanet.com/red-burner-2',
            'https://it.wikipedia.org/wiki/Pagina_principale',
            'http://icanhazip.com/',
            'http://italiaspecialiofferta.com/asdone/',
            'https://italiaspecialiofferta.com/asdone/',
            'https://www.italiaspecialiofferta.com/asdone/',
            'http://www.italiaspecialiofferta.com/asdone/',
        ];
        $flow = \App\Flow::findOrFail(35);
        foreach ($tests as $test) {
            echo $test . ' isExternal: ' . ($flow->urlIsExternal($test) ? 'true' : 'false') . '<br>';
        }


        //$tests = [
        //    'apps/italianumero3/public/ciaone1/index.html',
        //    '/italianumero3/public/ciaone2/index.html',
        //    'italianumero3/public/ciaone3/index.html',
        //    '/public/ciaone4/index.html',
        //    'public/ciaone5/index.html',
        //    'ciaone6/index.html',
        //    'ciaone7',
        //];
        //$domain = \App\Domain::findOrFail(8);
        //foreach ($tests as $test) {
        //    echo $domain->getUrlFromPath($test) . '<br>';
        //}

        //$php = 'php';
        //$command = $php . ' ../artisan queue:work --queue=zarocloner --stop-when-empty';
        //$commands = explode(' ', $command);
        //$process = new Process($commands);
        //$process->start();
        //while ($process->isRunning()) {
        //}
        //echo $process->getOutput();
        //echo $process->getErrorOutput();
        //exit();
        //dd($process->getOutput());
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
