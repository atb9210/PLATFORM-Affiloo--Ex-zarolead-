<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FacebookRule extends Model
{
    protected $table = 'third_facebook_rules';

    protected $fillable = [
        'name', 'value', 'status', 'frequency',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function apply()
    {
        // DEBUG
        Log::info('Facebook: Applying rule ' . $this->name . ' (' . $this->id . ')...');

        $accessToken = $this->value['access_token'];
        $ad_account_id = $this->value['ad_account_id'];
        $campaign_ids = $this->value['campaign_ids'];
        if (gettype($campaign_ids) == 'string') {
            $campaign_ids = json_decode($campaign_ids, true);
        }
        $all_campaigns = false;
        if (isset($this->value['all_campaigns'])) {
            $all_campaigns = $this->value['all_campaigns'];
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
            'metrics' => [
                'spend',
                'impressions',
                'cpm',
            ],
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

        $fb = new \Driverfury\Facebook\Facebook($accessToken);
        $adsets = $fb->getAdsets($ad_account_id, $reports);
        foreach ($adsets as $adset) {
            if (!$all_campaigns && !in_array($adset['campaign_id'], $campaign_ids)) {
                continue;
            }

            $flag = true;
            foreach ($rules as $rule) {
                // TODO
                /*
                // Custom metrics
                if ($rule['metric'] == 'lifetime_spend') {
                    if (!isset($adset['reports']['metrics']['lifetime_spend'])) {
                        $lifetime_report = $fb->getObjectReport(
                            'adset',
                            strval($adset['adset_id']),
                            $ad_account_id,
                            ['spend']
                        );
                        if (isset($lifetime_report['metrics']['spend'])) {
                            $adset['reports']['metrics']['lifetime_spend'] = floatval($lifetime_report['metrics']['spend']);
                        } else {
                            $adset['reports']['metrics']['lifetime_spend'] = 0.0;
                        }
                    }
                } else if ($rule['metric'] == 'profit' || $rule['metric'] == 'roi') {
                    $profit = 0.0;
                    if (isset($adset['reports']['metrics']['profit'])) {
                        $profit = floatval($adset['reports']['metrics']['profit']);
                    } else {
                        $profit = floatval($adset['reports']['metrics']['total_form_value']) - 
                            floatval($adset['reports']['metrics']['spend']);
                    }
                    $adset['reports']['metrics']['profit'] = $profit;

                    $roi = 0.0;
                    if (floatval($adset['reports']['metrics']['spend']) > 0.0) {
                        $roi = ($profit / floatval($adset['reports']['metrics']['spend']))*100.0;
                    }
                    $adset['reports']['metrics']['roi'] = $roi;
                }
                */

                if (!isset($adset[$rule['metric']])) {
                    $flag = false;
                    break;
                }

                $value = $adset[$rule['metric']];
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
                    $currentBid = floatval($adset['bid_amount'])/100;
                    $bid = $currentBid + floatval($action['value']);
                    $bid = min($bid, floatval($action['max_value']));
                    $fb->updateAdsetBidAmount($adset['id'], $bid);
                } else if ($action['type'] == 'dec_bid') {
                    // Decrease bid
                    Log::info('Dec bid');
                    $currentBid = floatval($adset['bid_amount'])/100;
                    $bid = $currentBid - floatval($action['value']);
                    $bid = max($bid, floatval($action['max_value']));
                    $fb->updateAdsetBidAmount($adset['id'], $bid);
                } else if ($action['type'] == 'inc_budget') {
                    // Increase budget
                    Log::info('Inc budget');
                    $currentBudget = floatval($adset['daily_budget'])/100;
                    $budget = $currentBudget + floatval($action['value']);
                    $budget = min($budget, floatval($action['max_value']));
                    $fb->updateAdsetBudget($adset['id'], $budget);
                } else if ($action['type'] == 'dec_budget') {
                    // Decrease budget
                    Log::info('Dec budget');
                    $currentBudget = floatval($adset['daily_budget'])/100;
                    $budget = $currentBudget - floatval($action['value']);
                    $budget = min($budget, floatval($action['max_value']));
                    $fb->updateAdsetBudget($adset['id'], $budget);
                } else if ($action['type'] == 'enable') {
                    // Enable adset
                    Log::info('Enable');
                    $fb->updateStatus($adset['id'], 'ACTIVE');
                } else if ($action['type'] == 'disable') {
                    // Disable adset
                    Log::info('Disable');
                    $fb->updateStatus($adset['id'], 'PAUSED');
                }
            }
        }
    }
}
