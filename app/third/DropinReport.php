<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class DropinReport extends Model
{
    protected $table = 'third_dropin_reports';

    public $timestamps = false;

    protected $fillable = [
        'day',
        'open_orders', 'closed_orders',
        'marketing', 'shipping', 'cogs',
        'order_value', 'revenue', 'net_revenue', 'vat', 'profit', 'cdv',
        'notes',
    ];

    protected $casts = [
        'open_orders' => 'int',
        'closed_orders' => 'int',

        'marketing' => 'float',
        'shipping' => 'float',
        'cogs' => 'float',

        'order_value' => 'float',
        'revenue' => 'float',
        'net_revenue' => 'float',
        'vat' => 'float',
        'profit' => 'float',
        'cdv' => 'float',
    ];

    public static function findByDay($day): DropinReport
    {
        $res = DropinReport::where('day', $day)->first();
        if ($res === null) {
            $res = DropinReport::create([
                'day' => $day,
            ]);
        }
        return $res;
    }

    public static function refreshReport($day): DropinReport
    {
        $report = DropinReport::findByDay($day);

        $params = [
            'open_orders' => 0,
            'closed_orders' => 0,

            'marketing' => 0.0,
            'shipping' => 0.0,
            'cogs' => 0.0,

            'order_value' => 0.0,
            'revenue' => 0.0,
            'net_revenue' => 0.0,
            'vat' => 0.0,
            'profit' => 0.0,
            'cdv' => 0.0,
        ];

        /*
        $shippings = DropinShipping::where('created_at', $day)->get();
        foreach ($shippings as $shipping) {
            $info = $shipping->info;
            if ($info === null) {
                $info = [];
            }

            $codAmount = 0.0;
            if (isset($info['cod_amount']) && $info['cod_amount'] != '') {
                $codAmount = floatval($info['cod_amount']);
            }
            $vatAmount = $codAmount - (($codAmount/122) * 100);

            $params['order_value'] += $codAmount;
            $params['vat'] += $vatAmount;
        }
        */

        $fromDate = date('Y-m-d', strtotime('-45 days', strtotime($day)));
        $toDate   = date('Y-m-d', strtotime('+45 days', strtotime($day)));
        $shippings = DropinShipping::where('created_at', '>=', $fromDate)
            ->where('created_at', '<=', $toDate)->get();
        $leads = DropinLead::where('created_at', '>=', $fromDate . ' 00:00:00')
            ->where('created_at', '<=', $toDate . ' 23:59:59')->get();
        foreach ($shippings as $shipping) {
            if ($shipping->payment_method != 'cod') {
                continue;
            }
            $info = $shipping->info;
            if ($info === null) {
                $info = [];
            }

            $deliveredAt = $shipping->getDeliveryDate();
            if ($deliveredAt === null || $deliveredAt != $day) {
                continue;
            }

            /*
            if (!isset($info['order_id'])) {
                continue;
            }
            $orderId = strval($info['order_id']);
            $lead = null;
            foreach ($leads as $l) {
                $leadInfo = $l->info;
                if ($leadInfo === null) {
                    continue;
                }
                if (!isset($leadInfo['order_id'])) {
                    continue;
                }
                $oId = strval($leadInfo['order_id']);
                if ($oId == $orderId) {
                    $lead = $l;
                    break;
                }
            }
            if ($lead == null) {
                continue;
            }
            $amount = 0.0;
            if (isset($leadInfo['total'])) {
                $amount = $leadInfo['total'];
            }
            */

            $amount = 0.0;
            if (isset($info['cod_amount']) && $info['cod_amount'] != '') {
                $amount = floatval($info['cod_amount']);
            }

            $params['revenue'] += $amount;
        }

        $report->update($params);

        return $report;
    }
}
