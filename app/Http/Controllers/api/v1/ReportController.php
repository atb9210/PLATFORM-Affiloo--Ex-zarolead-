<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Order as Order;
use App\User as User;

class ReportController extends Controller
{
    /**
     * Get users report.
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     * @urlParam filter_user int User's id
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
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
            'filter_user' => 'integer|exists:users,id',
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
        if (isset($params['filter_user'])) {
            $orders = $orders->where('user_id', $params['filter_user']);
        }

        // Calculate report for each user
        $reports = [];
        $usersEmailReports = []; // Email => reports array index
        foreach ($orders->get() as $order) {
            $user = $order->user;
            if (!array_key_exists($user->email, $usersEmailReports)) {
                $usersEmailReports[$user->email] = count($reports);
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
            $index = $usersEmailReports[$user->email];
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
}
