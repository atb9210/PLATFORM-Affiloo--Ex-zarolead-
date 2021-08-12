<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TikTokRule extends Model
{
    protected $table = 'third_tiktok_rules';

    protected $fillable = [
        'name', 'value', 'status', 'frequency',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function apply()
    {
        // DEBUG
        Log::info('Applying rule ' . $this->name . ' (' . $this->id . ')...');

        $advertiser_id = $this->value['advertiser_id'];
        $campaign_ids = $this->value['campaign_ids'];
        if (gettype($campaign_ids) == 'string') {
            $campaign_ids = json_decode($campaign_ids, true);
        }
        $rules = $this->value['rules'];
        if (gettype($rules) == 'string') {
            $rules = json_decode($rules, true);
        }
        $actions = $this->value['actions'];
        if (gettype($actions) == 'string') {
            $actions = json_decode($actions, true);
        }
        $time = 'lifetime';
        if (isset($this->value['time'])) {
            $time = $this->value['time'];
        }

        $reports = [
            'metrics' => ['spend', 'conversion', 'cost_per_conversion', 'conversion_rate', 'cpm', 'impressions'],
        ];
        if ($time == 'current_day') {
            $from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            $to   = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            $reports['start_date'] = $from;
            $reports['end_date'] = $to;
        } else if ($time == 'last_1_days') {
            $from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
            $to   = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            $reports['start_date'] = $from;
            $reports['end_date'] = $to;
        } else if ($time == 'last_2_days') {
            $from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-2, date("Y")));
            $to   = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            $reports['start_date'] = $from;
            $reports['end_date'] = $to;
        } else if ($time == 'last_3_days') {
            $from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-3, date("Y")));
            $to   = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            $reports['start_date'] = $from;
            $reports['end_date'] = $to;
        } else if ($time == 'last_7_days') {
            $from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-7, date("Y")));
            $to   = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            $reports['start_date'] = $from;
            $reports['end_date'] = $to;
        } else if ($time == 'last_14_days') {
            $from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-14, date("Y")));
            $to   = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            $reports['start_date'] = $from;
            $reports['end_date'] = $to;
        } else if ($time == 'last_30_days') {
            $from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-30, date("Y")));
            $to   = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            $reports['start_date'] = $from;
            $reports['end_date'] = $to;
        }

        $tt = new \Driverfury\TikTok\TikTok(env('TIKTOK_API_KEY'));
        $adgroups = $tt->getAdgroups($advertiser_id, '', $reports);
        foreach ($adgroups as $adgroup) {
            if (!in_array($adgroup['campaign_id'], $campaign_ids)) {
                continue;
            }

            $flag = true;
            foreach ($rules as $rule) {
                if (!isset($adgroup['reports']['metrics'][$rule['metric']])) {
                    $flag = false;
                    break;
                }
                $value = $adgroup['reports']['metrics'][$rule['metric']];
                $cmp = $rule['comparison'];

                if ($cmp == '<') {
                    if (!($value < $rule['value'])) {
                        $flag = false;
                        break;
                    }
                } else if ($cmp == '<=') {
                    if (!($value <= $rule['value'])) {
                        $flag = false;
                        break;
                    }
                } else if ($cmp == '==') {
                    if (!($value == $rule['value'])) {
                        $flag = false;
                        break;
                    }
                } else if ($cmp == '>=') {
                    if (!($value >= $rule['value'])) {
                        $flag = false;
                        break;
                    }
                } else if ($cmp == '>') {
                    if (!($value > $rule['value'])) {
                        $flag = false;
                        break;
                    }
                } else {
                    $flag = true;
                    break;
                }
            }

            if(!$flag) {
                continue;
            }

            foreach ($actions as $action) {
                if ($action['type'] == 'inc_bid') {
                    // Increate bid
                    Log::info('Inc bid');
                    $bid = floatval($adgroup['bid'] + $action['value']);
                    $bid = min($bid, floatval($action['max_value']));
                    $tt->updateAdgroupBid(
                        $advertiser_id,
                        $adgroup['adgroup_id'],
                        floatval($bid));
                } else if ($action['type'] == 'dec_bid') {
                    // Decrease bid
                    Log::info('Dec bid');
                    $bid = floatval($adgroup['bid'] - $action['value']);
                    $bid = max($bid, floatval($action['max_value']));
                    $tt->updateAdgroupBid(
                        $advertiser_id,
                        $adgroup['adgroup_id'],
                        floatval($bid));
                } else if ($action['type'] == 'inc_budget') {
                    // Increase budget
                    Log::info('Inc budget');
                    $budget = floatval($adgroup['budget'] + $action['value']);
                    $budget = min($budget, floatval($action['max_value']));
                    $tt->updateAdgroupBudget(
                        $advertiser_id,
                        $adgroup['adgroup_id'],
                        floatval($budget));
                } else if ($action['type'] == 'dec_budget') {
                    // Decrease budget
                    Log::info('Dec budget');
                    $budget = floatval($adgroup['budget'] - $action['value']);
                    $budget = max($budget, floatval($action['max_value']));
                    $tt->updateAdgroupBudget(
                        $advertiser_id,
                        $adgroup['adgroup_id'],
                        floatval($budget));
                } else if ($action['type'] == 'enable') {
                    // Enable adgroup
                    $tt->updateAdgroupStatus(
                        $advertiser_id,
                        $adgroup['adgroup_id'],
                        'ENABLE');
                } else if ($action['type'] == 'disable') {
                    // Disable adgroup
                    $tt->updateAdgroupStatus(
                        $advertiser_id,
                        $adgroup['adgroup_id'],
                        'DISABLE');
                }
            }
        }
    }
}
