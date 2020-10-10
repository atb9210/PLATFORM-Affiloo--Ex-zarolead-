<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Order as Order;
use App\User as User;
use App\UserReport as UserReport;
use App\Http\Resources\api\v1\UserReportResource;
use App\UserMonthlyReport as UserMonthlyReport;
use App\Http\Resources\api\v1\UserMonthlyReportResource;

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
     * @urlParam filter_product int Product's id
     * @urlParam filter_advertiser int Advertiser's id
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
            'filter_product' => 'integer|exists:products,id',
            'filter_advertiser' => 'integer|exists:advertisers,id',
        ]);

        // Retrieve orders
        $orders = Order::query();
        $orders = $orders->where('archived', 0);
        if (isset($params['filter_date_from'])) {
            $orders = $orders->where('created_at', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $orders = $orders->where('created_at', '<=', $params['filter_date_to']);
        }
        if (isset($params['filter_product'])) {
            $orders = $orders->where('product_id', $params['filter_product']);
        }
        if (isset($params['filter_advertiser'])) {
            $orders = $orders->where('advertiser_id', $params['filter_advertiser']);
        }

        // Calculate report for each product
        $reports = [];
        $lookupTable = []; // Id => reports array index
        foreach ($orders->get() as $order) {
            $product = $order->product;
            if (!array_key_exists($product->id, $lookupTable)) {
                $lookupTable[$product->id] = count($reports);
                $reports[] = [
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                    ],
                    'leads' => 0,
                    'commissions' => 0.0,
                    'sub_commissions' => 0.0,
                ];
            }
            $index = $lookupTable[$product->id];
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
    }

    /**
     * Get monthly report.
     *
     * @urlParam user int User's id
     * @urlParam month int Month
     * @urlParam year int Year
     *
     * @response {
     *  "data": [report]
     * }
     */
    public function monthlyReport(Request $request)
    {
        if (!Auth::user()->hasPermission('report', 'users')) {
            return response([
                "message" => "You don't have permissions to access reports"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'user' => 'required|integer|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:0',
        ]);

        $user = User::findOrFail($params['user']);
        $monthlyReport = UserMonthlyReport::getObject($user, $params['month'], $params['year']);

        return new UserMonthlyReportResource($monthlyReport);
    }

    /**
     * Update monthly report.
     *
     * @urlParam user int User's id
     * @urlParam month int Month
     * @urlParam year int Year
     * @urlParam year int Year
     *
     * @response {
     *  "data": [report]
     * }
     */
    public function monthlyReport(Request $request)
    {
}
