<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Order as Order;
use App\User as User;
use App\Role as Role;
use App\UserReport as UserReport;
use App\Http\Resources\api\v1\UserReportResource;
use App\FormStats as FormStats;
use App\AdvertiserReport as AdvertiserReport;
use App\Http\Resources\api\v1\AdvertiserReportResource;

class ReportController extends Controller
{
    /**
     * Get users report.
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     * @urlParam filter_user int User's id
     * @urlParam filter_roles array Users' roles
     *
     * @response {
     *  "data": [report]
     * }
     */
    public function users(Request $request)
    {
        if (!Auth::user()->hasPermission('report', 'users')) {
            return response([
                "message" => "You don't have permissions to access reports"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
            'filter_user' => 'integer|exists:users,id',
            'filter_roles' => 'array',
            'filter_roles.*' => 'integer|exists:roles,id',
        ]);

        /*
        // Retrieve orders
        $orders = Order::query();
        $orders = $orders->where('archived', 0);
        if (isset($params['filter_date_from'])) {
            $orders = $orders->where('created_at', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $orders = $orders->where('created_at', '<=', $params['filter_date_to']);
        }
        if (isset($params['filter_user'])) {
            $orders = $orders->where('user_id', $params['filter_user']);
        }
        if (isset($params['filter_role'])) {
            $orders = $orders->join('role_user', 'role_user.user_id', '=', 'orders.user_id')
                ->where('role_user.role_id', $params['filter_role']);
        }

        // Calculate report for each user
        $reports = [];
        $lookupTable = []; // Email => reports array index
        foreach ($orders->get() as $order) {
            $user = $order->user;
            if (!array_key_exists($user->email, $lookupTable)) {
                $lookupTable[$user->email] = count($reports);
                $reports[] = [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                    ],
                    'leads' => 0,
                    'commissions' => 0.0,
                    'sub_commissions' => 0.0,
                ];
            }
            $index = $lookupTable[$user->email];
            $reports[$index]['leads'] += 1;
            $reports[$index]['commissions'] += (float)$order->commission;
            $reports[$index]['sub_commissions'] += (float)$order->sub_commission;
        }

        // Format commissions and sub_commission (2 decimal digits)
        for ($i = 0; $i < count($reports); $i++) {
            $reports[$i]['commissions'] = round($reports[$i]['commissions'], 2);
            $reports[$i]['sub_commissions'] = round($reports[$i]['sub_commissions'], 2);
        }

        return response([
            'data' => $reports
        ]);
        */

        $reports = UserReport::query();
        if (isset($params['filter_date_from'])) {
            $reports = $reports->where('day', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $reports = $reports->where('day', '<=', $params['filter_date_to']);
        }
        if (isset($params['filter_user'])) {
            $reports = $reports->where('user_id', $params['filter_user']);
        }
        if (isset($params['filter_roles'])) {
            $reports = $reports->join('role_user', 'role_user.user_id', '=', 'user_reports.user_id')
                ->whereIn('role_user.role_id', $params['filter_roles']);
        }

        // Calculate report for each user
        $toReturn = [];
        $lookupTable = []; // Email => toReturn array index
        foreach ($reports->get() as $report) {
            $user = $report->user;
            if (!array_key_exists($user->email, $lookupTable)) {
                $lookupTable[$user->email] = count($toReturn);
                $toReturn[] = [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                    ],
                    'leads' => 0,
                    'commission' => 0.0,
                    'cost' => 0.0,
                    'extra_cost' => 0.0,
                    'profit' => 0.0,
                ];
            }
            $index = $lookupTable[$user->email];
            $toReturn[$index]['leads'] += $report->leads;
            $toReturn[$index]['commission'] += (float)$report->commission;
            $toReturn[$index]['cost'] += (float)$report->cost;
            $toReturn[$index]['extra_cost'] += (float)$report->extra_cost;
            $toReturn[$index]['profit'] += (float)$report->profit;
        }

        // Format currencies (2 decimal digits)
        for ($i = 0; $i < count($toReturn); $i++) {
            $toReturn[$i]['commission'] = round($toReturn[$i]['commission'], 2);
            $toReturn[$i]['cost'] = round($toReturn[$i]['cost'], 2);
            $toReturn[$i]['extra_cost'] = round($toReturn[$i]['extra_cost'], 2);
            $toReturn[$i]['profit'] = round($toReturn[$i]['profit'], 2);
        }

        return response([
            'data' => $toReturn
        ]);
    }

    /**
     * Get products report.
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     *
     * @response {
     *  "data": [report]
     * }
     */
    public function products(Request $request)
    {
        if (!Auth::user()->hasPermission('report', 'products')) {
            return response([
                "message" => "You don't have permissions to access reports"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
            'filter_advertiser' => 'integer|exists:users,id',
        ]);

        // Retrieve orders
        $formsStats = FormStats::query();
        if (isset($params['filter_date_from'])) {
            $formsStats = $formsStats->where('day', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $formsStats = $formsStats->where('day', '<=', $params['filter_date_to']);
        }

        // Calculate report for each product
        $reports = [];
        $lookupTable = []; // Id => reports array index
        foreach ($formsStats->get() as $stats) {
            $product = $stats->form->product;
            if (!array_key_exists($product->id, $lookupTable)) {
                $lookupTable[$product->id] = count($reports);
                $reports[] = [
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                    ],
                    'visits' => 0,
                    'leads' => 0,
                    'total_commission' => 0.0,
                    'total_cost' => 0.0,
                ];
            }
            $index = $lookupTable[$product->id];
            $reports[$index]['visits'] += intval($stats->visits);
            $reports[$index]['leads'] += intval($stats->leads);
            $reports[$index]['total_commission'] += floatval($stats->total_commission);
            $reports[$index]['total_cost'] += (float)$stats->total_cost;
        }

        // Format currencies (2 decimal digits)
        for ($i = 0; $i < count($reports); $i++) {
            $reports[$i]['total_commission'] = round($reports[$i]['total_commission'], 2);
            $reports[$i]['total_cost'] = round($reports[$i]['total_cost'], 2);
        }

        return response([
            'data' => $reports
        ]);
    }

    /**
     * Get advertisers' monthly report.
     *
     * @urlParam month int required Month
     * @urlParam year int required Year
     *
     * @response {
     *  "data": [report]
     * }
     */
    public function advertisers(Request $request)
    {
        if (!Auth::user()->hasPermission('report', 'users')) {
            return response([
                "message" => "You don't have permissions to access reports"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:0',
        ]);

        $reports = [];

        $users = User::query();
        $role = Role::where('name', 'advertiser');
        if ($role->count() > 0) {
            $role = $role->first();
            $users = $users->join('role_user', 'role_user.user_id', '=', 'id')
                ->where('role_user.role_id', $role->id);
            foreach ($users->get() as $user) {
                $reports[] = AdvertiserReport::getObject($user, $params['month'], $params['year']);
            }
        }

        return AdvertiserReportResource::collection($reports);
    }

    /**
     * Update advertiser' monthly report
     *
     * @bodyParam month int required Month
     * @bodyParam year int required Year
     * @bodyParam approved_commission float required Approved commission
     *
     * @response {
     *  "data": report
     * }
     */
    public function advertisersUpdate($user_id, Request $request)
    {
        if (!Auth::user()->hasPermission('report', 'users')) {
            return response([
                "message" => "You don't have permissions to update reports"
            ], 403);
        }

        $params = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:0',
            'approved_commission' => 'required|numeric|min:0',
        ]);

        $user = User::findOrFail($user_id);
        $report = AdvertiserReport::getObject($user, $params['month'], $params['year']);
        if ($params['approved_commission'] > $report->commission) {
            return response([
                "message" => "Approved commission value cannot be greather than commission value"
            ], 422);
        }

        $report->approved_commission = $params['approved_commission'];
        $report->save();

        return new AdvertiserReportResource($report);
    }
}
