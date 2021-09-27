<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Order;
use App\OrderStatus;
use App\Form;
use App\Landing;

/*
// TODO: to delete (old code)
use App\Form as Form;
use App\FormStats as FormStats;
use App\User as User;
use App\UserReport as UserReport;
use App\AdvertiserReport as AdvertiserReport;
*/

class PostbackController extends Controller
{
    /**
     * Postback.
     *
     * @bodyParam sub_id int required Form's id
     * @bodyParam status int required Status
     *
     * @response {
     *  "message": "Postback received",
     * }
     */
    public function index(Request $request)
    {
        // DEBUG
        Log::channel('postback')->info('Postback request received: ' . json_encode($request->all()));

        // Param validation
        $params = $request->validate([
            'sub_id' => 'required|numeric|exists:orders,id',
            'status' => 'required|numeric',
        ]);
        $params['status'] = intval($params['status']);

        // DEBUG
        Log::channel('postback')->info('Params: ' . json_encode($params));

        $order = Order::findOrFail($params['sub_id']);
        $statuses = [
            0 => OrderStatus::WAITING,
            1 => OrderStatus::CONFIRMED,
            2 => OrderStatus::REFUSED,
            3 => OrderStatus::REFUSED,
        ];

        if (isset($statuses[$params['status']])) {
            $newStatus = $statuses[$params['status']];
            $order->order_status_id = $newStatus;
            $order->save();

            if ($newStatus == OrderStatus::CONFIRMED) {
                $info = $order->info;
                if (isset($info['form_id']) && isset($info['landing_id'])) {
                    $commission = $order->commission;
                    $sub_commission = 0.0;
                    if (isset($info['sub_commission'])) {
                        $sub_commission = floatval($info['sub_commission']);
                    }
                    $form = Form::findOrFail($info['form_id']);
                    $query_params = [];
                    if (isset($params['query_params'])) {
                        $query_params = $params['query_params'];
                        if (is_string($query_params)) {
                            $query_params = json_decode($query_params, true);
                        }
                    }
                    $landing = Landing::findOrFail($info['landing_id']);

                    $order->updateStats(
                        $commission, $sub_commission,
                        $form, $query_params,
                        $landing);
                }
            }
        }

        /*
        // TODO: to delete (old code)
        $form = Form::findOrFail($params['sub_id']);
        if ($params['status'] == 1) {
            $commission = 0.0;
            $sub_commission = 0.0;

            if (isset($form->product->quantity['options']) &&
                isset($form->product->quantity['options']['1']) &&
                isset($form->product->quantity['options']['1']['commission'])) {
                $commission = floatval($form->product->quantity['options']['1']['commission']);
            }
            if (isset($form->product->quantity['options']) &&
                isset($form->product->quantity['options']['1']) &&
                isset($form->product->quantity['options']['1']['sub_commission'])) {
                $sub_commission = floatval($form->product->quantity['options']['1']['sub_commission']);
            }

            // Increase form leads and commission
            $today = date('Y-m-d');
            $formStats = FormStats::getObject($form->id, $today);
            $formStats->leads += 1;
            $formStats->total_commission += $commission;
            $formStatsInfo = $formStats->info;
            if (!isset($formStatsInfo['total_sub_commission'])) {
                $formStatsInfo['total_sub_commission'] = 0.0;
            }
            $formStatsInfo['total_sub_commission'] += $sub_commission;
            $formStats->info = $formStatsInfo;
            $formStats->save();

            // Form total
            $form->leads += 1;
            $form->total_commission += $commission;
            $formInfo = $form->info;
            if (!isset($formInfo['total_sub_commission'])) {
                $formInfo['total_sub_commission'] = 0.0;
            }
            $formInfo['total_sub_commission'] += $sub_commission;
            $form->info = $formInfo;
            $form->save();

            // Update user's report stats
            $report = UserReport::getObject($form->user->id, $today);
            $report->leads += 1;
            $report->commission += $commission;
            $report->profit = $report->commission - ($report->cost + $report->extra_cost);
            $report->save();

            // Update admin's user report
            $adminRoleId = 2;
            $users = User::query();
            $users = $users->join('role_user', 'role_user.user_id', '=', 'id')
                ->whereIn('role_user.role_id', [$adminRoleId]);
            $users = $users->get();
            foreach ($users as $user) {
                // Skip if it is already updated
                if ($user->id == $form->user->id) {
                    continue;
                }
                $report = UserReport::getObject($user->id, $today);
                $report->leads += 1;
                $report->commission += $commission;
                $report->profit = $report->commission - ($report->cost + $report->extra_cost);
                $report->save();
            }

            // Update advertiser's report stats
            $timestamp = strtotime($today);
            $month = intval(date('m', $timestamp));
            $year = intval(date('Y', $timestamp));
            $advertiser = User::findOrFail($form->product->advertiser_id);
            $advReport = AdvertiserReport::getObject($advertiser, $month, $year);
            $advReport->leads += 1;
            $advReport->commission += $commission;
            $advReport->profit = $advReport->commission - $advReport->cost;
            $advReport->save();
        }
        */

        return response([
            'message' => 'Postback received',
        ]);
    }
}
