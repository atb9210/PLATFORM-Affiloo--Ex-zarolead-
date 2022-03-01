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

    private function generateRandomString(int $length = 32): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function test(Request $request)
    {
        $todayLeads = [
            '+(39)3804916544',
            '+(39)3488149818',
            '+(39)3317443111',
            '+(39)3405614088',
            '+(39)3487008053',
            '+(39)3421022680',
            '+(39)3393682737',
        ];
        $leads = \App\third\DropinLead::where('id', '>=', 69)
            ->where('id', '<=', 164)->get();
        $leadsDone = [];
        foreach ($leads as $lead) {
            if (in_array($lead->phone, $todayLeads)) {
                continue;
            }

            $info = $lead->info;
            if ($info === null) {
                $info = [];
            }
            $info['sku'] = '2433-911-18031';
            $info['product_id'] = 343;
            $info['product_name'] = 'Tazza Elettrica';
            $lead->info = $info;

            /*
            $wLead = \App\third\WarehouseLead::where('phone', $lead->phone)->where('archived', 0)->get()->last();
            if ($wLead !== null) {
                $lead->created_at = $wLead->created_at;
            }
            */

            $lead->save();

            $leadsDone[] = [
                'phone' => $lead->phone,
                'date'  => $lead->created_at,
            ];
        }
        dd([
            'count' => count($leadsDone),
            'leads' => $leadsDone,
        ]);
        exit();
        // TODO: HERE

        $tmpLeads = \App\third\WarehouseLead::where('created_at', '>=', '2022-01-01 00:00:00')
            ->whereIn('status', ['to do', 'confirmed', 'abandoned', 'to recall'])
            ->get();
        $warehouseLeads = [];
        foreach ($tmpLeads as $lead) {
            $info = $lead->info;
            if ($info === null) {
                $info = [];
            }

            $productName = null;
            if (isset($info['product_name'])) {
                $productName = $info['product_name'];
            }
            if ($productName === 'Tazza Elettrica') {
                $warehouseLeads[] = $lead;
            }

            if (!isset($info['gateway'])) {
                $info['gateway'] = 'cashondelivery';
                $info['payment_method'] = 'cashondelivery';
            }

            $info['price'] = '39.90';
            $info['total'] = '39.90';
            $info['sku'] = '2433-911-18031';
        }
        $dropinLeads = [];
        foreach ($warehouseLeads as $lead) {
            $dropinLeads[] = [
                'first_name' => $lead->first_name,
                'last_name' => $lead->last_name,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'status' => $lead->status,
                'info' => $info,
                'notes' => $lead->notes,
                'hash' => $this->generateRandomString(64),
                'archived' => $lead->archived,
                'created_at' => $lead->created_at,
                'updated_at' => $lead->updated_at,
            ];
        }
        $finalLeads = [];
        foreach ($dropinLeads as $lead) {
            $finalLeads[] = \App\third\DropinLead::create($lead);
        }
        dd($finalLeads);
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
