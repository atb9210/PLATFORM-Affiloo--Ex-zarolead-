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

    public function importToDropinLeadsTable(Request $request)
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
            /*
            unset($info['products']);
            $info['sku'] = '2433-911-18031';
            $info['product_id'] = 343;
            $info['product_name'] = 'Tazza Elettrica';
            */

            $wLead = \App\third\WarehouseLead::where('phone', $lead->phone)->where('archived', 0)->get()->last();
            if ($wLead !== null) {
                //$lead->created_at = $wLead->created_at;
                if ($wLead->info !== null && isset($wLead->info['shipping'])) {
                    $info['shipping'] = $wLead->info['shipping'];
                }
            }

            $lead->info = $info;

            /*
            $notes = $lead->notes;
            if ($notes === null) {
                $notes = '';
            }
            $notes = 'IMPORTED ' . $notes;
            $lead->notes = $notes;
            */

            $lead->save();

            $leadsDone[] = [
                'phone' => $lead->phone,
                'info'  => $lead->info,
                'notes' => $lead->notes,
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
    }

    public function test(Request $request)
    {
        //$cookies = 'XSRF-TOKEN=eyJpdiI6IndBS2dlU1Z5MmVKUDdZUXJZQndGRmc9PSIsInZhbHVlIjoiNkd3bnZLanlCeEtsbHFyVXp5SmlOdHd3T1l2aWdabGs4YjB3b3hwQTRUNXZWNmdJU0ZoQ2EwVTE2cDV4MzBJUkFBU2ZWYU5wWHpBdWt3WE9yTTA5MThhYnJkS2J1UkxjbE1kYVVXcXdUN28wUWN3M05WK0ZSL2pwRTFlUmpnb3QiLCJtYWMiOiI0MjgxODQzZmVkZjY2OWM2OTU0NjkwZmZiOTJhMWZiODYwODYzNjNjMzY2ODRjYTZhMjk4MTc0NmYzZGY5OTllIiwidGFnIjoiIn0%3D; expires=Thu, 03-Mar-2022 14:49:59 GMT; Max-Age=86400; path=/; samesite=lax, dropin_session=eyJpdiI6IkJQOWIzdDJ4bEtHR0xJd2Z6dzVJQnc9PSIsInZhbHVlIjoiYTgyaklIL1dOTkIwbmF2NkNtYktUY05odExmWURIeE13SnJRczk4dmRmOVZ6OEw4WEc3NVpPcldOdUdPY3g3cS9yb3BrL3FUUGZvam1ZemJ1Z0EzNTRhL09HVnhKR1hlWXFhOEdJTzlCbzVobUFWQ2ZGOVE2SWg1emlYd0l0Vk0iLCJtYWMiOiIxZjMzYjY0ZTJjOTMwZWRkZjUzMzE5MzdjYjI0OGQ3MDYwYjI5ZTM2OTQ0ZjgyN2YyYjY4N2IxYTkyZWY5YTg0IiwidGFnIjoiIn0%3D; expires=Thu, 03-Mar-2022 14:49:59 GMT; Max-Age=86400; path=/; httponly; samesite=lax';
        //dd(\Driverfury\Requests\Requests::parseCookies($cookies));
        //
        $dropin = new \Driverfury\Dropin\Dropin('zarosrls@gmail.com', 'medi9810');
        dd($dropin->getOrders());
        exit();

        $requests = new \Driverfury\Requests\Requests();

        $url = 'https://dropship.eurowholesale.eu/sanctum/csrf-cookie';
        $headers = [
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ];
        $response = $requests->get($url, $headers);
        dd($response);

        $cookies = $requests->getCookies();
        if (isset($cookies['XSRF-TOKEN'])) {
            $xsrfToken = $cookies['XSRF-TOKEN'];
            //$xsrfToken = str_replace('%3D', '=', $xsrfToken);
            $xsrfToken = str_replace('=', 'miaoasd', $xsrfToken);
            $headers['x-xsrf-token'] = $xsrfToken;
        }
        $headers['referer'] = 'https://dropship.eurowholesale.eu/auth/login';
        $headers['user-agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36';
        $headers['x-requested-with'] = 'XMLHttpRequest';

        $url = 'https://dropship.eurowholesale.eu/login';
        $response = $requests->post($url, $headers, [
            'email' => 'zarosrls@gmail.com',
            'password' => 'medi9810',
            'status' => false,
        ]);
        dd($response);

        $url = 'https://dropship.eurowholesale.eu/api/orders-list';
        $response = $requests->get($url, $headers);

        dd($response);
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
