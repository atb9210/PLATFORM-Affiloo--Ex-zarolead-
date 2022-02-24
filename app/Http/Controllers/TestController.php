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
    public function facebookMonitor(Request $request)
    {
        $monitorId = $request->input('monitor');
        $day = $request->input('day');
        $monitor = \App\third\FacebookMonitor::findOrFail($monitorId);
        $result = $monitor->createReport($day);
        if ($result) {
            return 'true';
        }
        return 'false';
    }

    public function facebookMonitorRunStrategy($id, Request $request)
    {
        $result = true;
        $monitorId = $id;
        $monitor = \App\third\FacebookMonitor::findOrFail($monitorId);
        if ($monitor !== null) {
            foreach ($monitor->strategy as $key => $value) {
                $params = $value;
                $params['action_type'] = $key;
                if (isset($value['enabled']) && $value['enabled']) {
                    $result = $result && $monitor->runStrategy($params);
                }
            }
        } else {
            $result = false;
        }
        if ($result) {
            return 'true';
        }
        return 'false';
    }

    public function test(Request $request)
    {
        $pathnames = [
            'pathname1' => 'ciao/index.php',
            'pathname2' => 'ciao_bambina/index.php',
            'pathname3' => 'ciao-bambina/index.php',
            'pathname4' => 'Guanto-led2x1/index.php',
        ];
        /*
        $pathnames = [
            'pathname1' => 'index_yo',
            'pathname2' => '-invalid',
            'pathname3' => '_this-is-valid_',
        ];
        */
        $fileOrDirNameRegex = '\w+[\w\d-]*';
        $flowPathnameRegex = '#^(' . $fileOrDirNameRegex . '/){1,2}(' . $fileOrDirNameRegex . ')\.\w+$#';
        $validator = Validator::make($pathnames, [
            'pathname1' => 'required|regex:' . $flowPathnameRegex,
            'pathname2' => 'required|regex:' . $flowPathnameRegex,
            'pathname3' => 'required|regex:' . $flowPathnameRegex,
            'pathname4' => 'required|regex:' . $flowPathnameRegex,
        ]);
        /*
        $validator = Validator::make($pathnames, [
            'pathname1' => 'required|regex:#^(\w+/){1,2}\w+\.\w+$#',
            'pathname2' => 'required|regex:#^(\w+/){1,2}\w+\.\w+$#',
            'pathname3' => 'required|regex:#^(\w+/){1,2}\w+\.\w+$#',
        ]);
         */
        if ($validator->fails()) {
            dd($validator->messages());
        } else {
            dd("OK");
        }
        exit();

        $monitor = \App\third\FacebookMonitor::find(4);
        dd($monitor->runStrategy([
            'action_type' => 'bid.down',
            'variation' => -1,
        ]));
        exit();
        dd(\App\third\FacebookMonitor::find(3)->getReports('2022-02-09', '2022-02-10'));
        exit();

        dd(strval(82));
        exit();

        $dateStart = date('Y-m-d', strtotime('-7 day'));
        $dateEnd   = date('Y-m-d');
        $leads = \App\third\WarehouseLead::where('archived', 0)
            ->where('created_at', '>=', $dateStart . ' 00:00:00')
            ->where('created_at', '<=', $dateEnd   . ' 23:59:59')
            ->where(function($query) {
                $query->where('status', 'to recall')
                    ->orWhere('status', 'no answer');
            })
            ->get();
        dd($leads);
        $d = new \DateTime();
        dd($d->format(\DateTime::ISO8601));
        exit();
    }

    public function test2(Request $request)
    {
        dd($request->all());
    }
    
    // DEBUG TODO: to delete
    public function testCheckout()
    {
        return view('checkout');
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
