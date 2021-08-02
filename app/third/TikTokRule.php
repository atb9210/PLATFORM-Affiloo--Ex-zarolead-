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

        $reports = [
            'metrics' => ['spend', 'conversion', 'cost_per_conversion', 'conversion_rate', 'cpm', 'impressions'],
        ];

        $tt = new \Driverfury\TikTok\TikTok(env('TIKTOK_API_KEY'));
        $allCampaigns = $tt->getCampaigns(env('TIKTOK_ADVERTISER_ID'), $reports);

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

        $campaigns = [];
        foreach ($allCampaigns as $campaign) {
            if (in_array($campaign['campaign_id'], $campaign_ids)) {
                $campaigns[] = $campaign;
            }
        }

        foreach ($campaigns as $campaign) {
            $flag = true;
            foreach ($rules as $rule) {
                if (!isset($campaign['reports']['metrics'][$rule['metric']])) {
                    $flag = false;
                    break;
                }
                $value = $campaign['reports']['metrics'][$rule['metric']];
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

            $allAdgroups = $tt->getAdgroups(env('TIKTOK_ADVERTISER_ID'));
            $adgroups = [];
            foreach ($allAdgroups as $adgroup) {
                if (in_array($adgroup['campaign_id'], $campaign_ids)) {
                    $adgroups[] = $adgroup;
                }
            }

            foreach ($adgroups as $adgroup) {
                foreach ($actions as $action) {
                    if ($action['type'] == 'inc_bid') {
                        // Increate bid
                        Log::info('Inc bid');
                        $bid = floatval($adgroup['bid'] + $action['value']);
                        $bid = min($bid, floatval($action['max_value']));
                        $tt->updateAdgroupBid(
                            env('TIKTOK_ADVERTISER_ID'),
                            $adgroup['adgroup_id'],
                            floatval($bid));
                    } else if ($action['type'] == 'dec_bid') {
                        // Decrease bid
                        Log::info('Dec bid');
                        $bid = floatval($adgroup['bid'] - $action['value']);
                        $bid = max($bid, floatval($action['max_value']));
                        $tt->updateAdgroupBid(
                            env('TIKTOK_ADVERTISER_ID'),
                            $adgroup['adgroup_id'],
                            floatval($bid));
                    } else if ($action['type'] == 'inc_budget') {
                        // Increase budget
                        Log::info('Inc budget');
                        $budget = floatval($adgroup['budget'] + $action['value']);
                        $budget = min($budget, floatval($action['max_value']));
                        $tt->updateAdgroupBudget(
                            env('TIKTOK_ADVERTISER_ID'),
                            $adgroup['adgroup_id'],
                            floatval($budget));
                    } else if ($action['type'] == 'dec_budget') {
                        // Decrease budget
                        Log::info('Dec budget');
                        $budget = floatval($adgroup['budget'] - $action['value']);
                        $budget = max($budget, floatval($action['max_value']));
                        $tt->updateAdgroupBudget(
                            env('TIKTOK_ADVERTISER_ID'),
                            $adgroup['adgroup_id'],
                            floatval($budget));
                    }
                }
            }
        }
    }
}
