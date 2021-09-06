<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Driverfury\TikTok\TikTok;

use App\third\TikTokRule;
use App\Http\Resources\api\third\TikTokRuleResource;

use App\third\TikTokAdvertiser;
use App\Http\Resources\api\third\TikTokAdvertiserResource;

class TikTokController extends Controller
{
    /**
     * Get advertisers list
     *
     * @response {
     *  "data": [advertisers],
     * }
     */
    public function advertisers(Request $request)
    {
        // TODO: Authorization

        $advertisers = TikTokAdvertiser::all();
        return response([
            'data' => $advertisers,
        ]);
    }

    /**
     * Create advertiser
     *
     * @bodyParam id string required Advertiser's id
     * @bodyParam name string required Advertiser's name
     *
     * @response {
     *  "message": "Advertiser has been successfully created",
     *  "data": advertiser,
     * }
     */
    public function storeAdvertiser(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'id' => 'required|string|unique:third_tiktok_advertisers,id',
            'name' => 'required|string|min:1',
        ]);

        $advertiser = TikTokAdvertiser::create($params);
        return response([
            'message' => 'Advertiser has been successfully created',
            'data' => $advertiser,
        ]);
    }

    /**
     * Update advertiser
     *
     * @urlParam id string required Advertiser's id
     *
     * @bodyParam id string Advertiser's new id
     * @bodyParam name string Advertiser's new name
     *
     * @response {
     *  "data": advertiser,
     * }
     */
    public function updateAdvertiser($id, Request $request)
    {
        // TODO: Authorization

        $advertiser = TikTokAdvertiser::findOrFail($id);

        // Param validation
        $params = $request->validate([
            'id' => 'string',
            'name' => 'string|min:1',
        ]);

        if (isset($params['id']) && $id != $params['id']) {
            if (TikTokAdvertiser::where('id', $params['id'])->get()->count() > 0) {
                return response([
                    'message' => 'Advertiser\'s id already used',
                    'errors'  => [
                        'id' => [ 'Advertiser\'s id already used' ],
                    ],
                ], 400);
            }
        }

        $advertiser->update($params);

        return response([
            'data' => $advertiser,
        ]);
    }

    /**
     * Delete advertiser
     *
     * @urlParam id string required Advertiser's id
     *
     * @response {
     *  "message": "Advertiser deleted",
     * }
     */
    public function destroyAdvertiser($id, Request $request)
    {
        // TODO: Authorization

        $advertiser = TikTokAdvertiser::findOrFail($id);
        $advertiser->delete();
        return response([
            'message' => 'Advertiser deleted',
        ]);
    }

    /**
     * Get campaigns list
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam reports bool Reports (default: true)
     * @bodyParam filter_date_from string From date (Y-m-d format)
     * @bodyParam filter_date_to string To date (Y-m-d format)
     *
     * @response {
     *  "data": [campaigns],
     * }
     */
    public function campaigns(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'advertiser_id' => 'required',
            'reports' => 'nullable|boolean',
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $reports = [];
        if (!isset($params['reports']) || boolval($params['reports']) === true) {
            $reports = [
                'metrics' => [
                    'total_form_value',
                    'spend',
                    'conversion',
                    'cost_per_conversion',
                    'conversion_rate',
                    'cpm',
                    'impressions'
                ],
            ];
        }

        if (isset($params['filter_date_from'])) {
            $reports['start_date'] = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $reports['end_date'] = $params['filter_date_to'];
        }

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        $campaigns = $tt->getCampaigns($params['advertiser_id'], $reports);

        return response([
            'data' => $campaigns,
        ]);
    }

    /**
     * Get adgroups list
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam filter_date_from string From date (Y-m-d format)
     * @bodyParam filter_date_to string To date (Y-m-d format)
     * @bodyParam filter_campaign integer Campaign (parent) id
     *
     * @response {
     *  "data": [adgroups],
     * }
     */
    public function adgroups(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'advertiser_id' => 'required',
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
            'filter_campaign' => 'numeric|min:1',
        ]);

        $reports = [
            'metrics' => [
                'total_form_value',
                'spend',
                'conversion',
                'cost_per_conversion',
                'conversion_rate',
                'cpm',
                'impressions'
            ],
        ];

        if (isset($params['filter_date_from'])) {
            $reports['start_date'] = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $reports['end_date'] = $params['filter_date_to'];
        }
        $parentId = '';
        if (isset($params['filter_campaign'])) {
            $parentId = strval($params['filter_campaign']);
        }

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        $adgroups = $tt->getAdgroups($params['advertiser_id'], $parentId, $reports);

        return response([
            'data' => $adgroups,
        ]);
    }

    /**
     * Get ads list
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam filter_date_from string From date (Y-m-d format)
     * @bodyParam filter_date_to string To date (Y-m-d format)
     * @bodyParam filter_adgroup integer Adgroup (parent) id
     *
     * @response {
     *  "data": [ads],
     * }
     */
    public function ads(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'advertiser_id' => 'required',
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
            'filter_adgroup' => 'numeric|min:1',
        ]);

        $reports = [
            'metrics' => [
                'total_form_value',
                'spend',
                'conversion',
                'cost_per_conversion',
                'conversion_rate',
                'cpm',
                'impressions'
            ],
        ];

        if (isset($params['filter_date_from'])) {
            $reports['start_date'] = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $reports['end_date'] = $params['filter_date_to'];
        }
        $parentId = '';
        if (isset($params['filter_adgroup'])) {
            $parentId = strval($params['filter_adgroup']);
        }

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        $ads = $tt->getAds($params['advertiser_id'], $parentId, $reports);

        return response([
            'data' => $ads,
        ]);
    }

    /**
     * Update campaign
     *
     * @urlParam id string required Campaign id
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam status string New campaign status
     *
     * @response {
     *  "message": "Campaign updated",
     * }
     */
    public function updateCampaign(string $id, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'advertiser_id' => 'required',
            'status' => 'in:DELETE,DISABLE,ENABLE',
        ]);

        $success = true;

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        if ($success && isset($params['status'])) {
            if(!$tt->updateCampaignStatus(
                $params['advertiser_id'],
                $id, $params['status'])) {
                $success = false;
            }
        }

        if($success) {
            return response([
                'message' => 'Campaign updated',
            ]);
        }
        return response([
            'message' => 'Cannot update campaign',
        ], 500);
    }

    /**
     * Update adgroup
     *
     * @urlParam id string required Adgroup id
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam status string New adgroup status
     * @bodyParam bid_strategy string New adgroup bid_strategy
     * @bodyParam bid float New adgroup bid
     *
     * @response {
     *  "message": "Adgroup updated",
     * }
     */
    public function updateAdgroup(string $id, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'advertiser_id' => 'required',
            'status' => 'in:DELETE,DISABLE,ENABLE',
            'budget' => 'numeric|min:0',
            'bid_strategy' => 'string|in:BID_TYPE_CUSTOM,BID_TYPE_MAX_CONVERSION,BID_TYPE_NO_BID',
            'bid' => 'numeric|min:0',
        ]);

        $success = true;

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        if ($success && isset($params['status'])) {
            if(!$tt->updateAdgroupStatus(
                $params['advertiser_id'],
                $id, $params['status'])) {
                $success = false;
            }
        }
        if ($success && isset($params['budget'])) {
            $params['budget'] = floatval($params['budget']);
            if(!$tt->updateAdgroupBudget(
                $params['advertiser_id'],
                $id, $params['budget'])) {
                $success = false;
            }
        }
        if ($success && isset($params['bid']) && isset($params['bid_strategy'])) {
            if(!$tt->updateAdgroupBid(
                $params['advertiser_id'], $id,
                $params['bid_strategy'], floatval($params['bid']))) {
                $success = false;
            }
        }

        if ($success) {
            return response([
                'message' => 'Adgroup updated',
            ]);
        }
        return response([
            'message' => 'Cannot update adgroup',
        ], 500);
    }

    /**
     * Update ad
     *
     * @urlParam id string required Ad id
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam status string New ad status
     *
     * @response {
     *  "message": "Ad updated",
     * }
     */
    public function updateAd(string $id, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'advertiser_id' => 'required',
            'status' => 'in:DELETE,DISABLE,ENABLE',
        ]);

        $success = true;

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        if($success && isset($params['status'])) {
            if(!$tt->updateAdStatus(
                $params['advertiser_id'],
                $id, $params['status'])) {
                $success = false;
            }
        }

        if($success) {
            return response([
                'message' => 'Ad updated',
            ]);
        }
        return response([
            'message' => 'Cannot update ad',
        ], 500);
    }

    /**
     * Get image info
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam image_id string required Image id
     *
     * @response {
     *  "data": image
     * }
     */
    public function imageInfo(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'advertiser_id' => 'required',
            'image_id' => 'required|string|min:1',
        ]);

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        $image = $tt->getImageInfo(
            $params['advertiser_id'],
            $params['image_id']);

        if (empty($image)) {
            return response([
                'message' => 'Cannot get image info',
            ], 401);
        } elseif (isset($image['code']) && $image['code'] != 0) {
            return response([
                'message' => $image['message'],
            ], 401);
        }

        return response([
            'data' => $image,
        ]);
    }

    /**
     * Trigger event
     *
     * @bodyParam token string required Token
     * @bodyParam pixel string required Pixel id
     * @bodyParam event string required Event type
     * @bodyParam ttclid string Click id
     * @bodyParam properties json Properties
     *
     * @response {
     *  "message": "Event has been triggered",
     * }
     */
    public function triggerEvent(Request $request)
    {
        $events = [
            'ViewContent',
            'ClickButton',
            'Search',
            'AddToWishlist',
            'AddToCart',
            'InitiateCheckout',
            'AddPaymentInfo',
            'CompletePayment',
            'PlaceOrder',
            'Contact',
            'Download',
            'SubmitForm',
            'CompleteRegistration',
            'Subscribe',
        ];
        $eventsList = implode(',', $events);

        // Param validation
        $params = $request->validate([
            'token' => 'required|string|min:1',
            'pixel' => 'required|string|min:1',
            'event' => 'required|string|min:1|in:' . $eventsList,
            'ttclid' => 'nullable|string',
            'properties' => 'json',
        ]);

        $token = $params['token'];
        $pixel = $params['pixel'];
        $event = $params['event'];
        $ttclid = '';
        if (isset($params['ttclid']) && $params['ttclid'] != null && $params['ttclid'] != '') {
            $ttclid = $params['ttclid'];
        }
        $properties = [];
        if (isset($params['properties'])) {
            $properties = json_decode($params['properties'], true);
        }

        $tt = new \Driverfury\TikTok\TikTok($token);
        $tt->triggerEvent($pixel, $event, $ttclid, $properties);

        return response([
            'message' => 'Event has been triggered',
        ]);
    }

    /**
     * Get rules
     *
     * @response {
     *  "data": [rules]
     * }
     */
    public function rules(Request $request)
    {
        // TODO: Authorization

        $rules = TikTokRule::all();

        return TikTokRuleResource::collection($rules);
    }

    /**
     * Create rule
     *
     * @bodyParam name string required Rule's name
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam all_campaigns bool Apply the rule to every campaign
     * @bodyParam campaign_ids array required Campaigns ids
     * @bodyParam rules string required Rules
     * @bodyParam actions string required Actions
     * @bodyParam status string Status
     * @bodyParam frequency string required Frequency
     * @bodyParam time string Time
     *
     * @response {
     *  "message": "Rule created",
     *  "data": rule
     * }
     */
    public function createRule(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'name' => 'string|required',
            'advertiser_id' => 'string|required|exists:third_tiktok_advertisers,id',
            'all_campaigns' => 'boolean',
            'campaign_ids' => 'array|required',
            'rules' => 'json|required',
            'actions' => 'json|required',
            'status' => 'in:active,disabled',
            'frequency' => 'required|in:every_five_minutes,every_ten_minutes,every_fifteen_minutes,every_thirty_minutes,hourly,every_two_hours,every_three_hours,daily',
            'time' => 'string',
        ]);

        $advertiser_id = $params['advertiser_id'];
        $all_campaigns = false;
        if (isset($params['all_campaigns'])) {
            $all_campaigns = ($params['all_campaigns'] == true);
        }
        $campaign_ids = $params['campaign_ids'];
        $rules = $params['rules'];
        $actions = $params['actions'];
        $time = 'lifetime';
        if (isset($params['time'])) {
            $time = $params['time'];
        }

        // TODO: Check campaign_ids, rules and actions

        $rule = TikTokRule::create([
            'name' => $params['name'],
            'value' => [
                'advertiser_id' => $advertiser_id,
                'all_campaigns' => $all_campaigns,
                'campaign_ids' => $campaign_ids,
                'rules' => $rules,
                'actions' => $actions,
                'time' => $time,
            ],
        ]);

        return response([
            'message' => 'Rule created',
            'data' => $rule,
        ]);
    }

    /**
     * Update rule
     *
     * @urlParam id int required Rule's id
     *
     * @bodyParam name string Rule's name
     * @bodyParam advertiser_id string Advertiser's id
     * @bodyParam all_campaigns bool Apply the rule to every campaign
     * @bodyParam campaign_ids string Campaigns ids
     * @bodyParam rules string Rules
     * @bodyParam actions string Actions
     * @bodyParam status string Status
     * @bodyParam frequency string Frequency
     * @bodyParam time string Time
     *
     * @response {
     *  "message": "Rule updated",
     *  "data": rule
     * }
     */
    public function updateRule($id, Request $request)
    {
        // TODO: Authorization

        $rule = TikTokRule::findOrFail($id);

        // Param validation
        $params = $request->validate([
            'name' => 'string',
            'advertiser_id' => 'string|exists:third_tiktok_advertisers,id',
            'all_campaigns' => 'boolean',
            'campaign_ids' => 'array',
            'rules' => 'json',
            'actions' => 'json',
            'status' => 'in:active,disabled',
            'frequency' => 'in:every_five_minutes,every_ten_minutes,every_fifteen_minutes,every_thirty_minutes,hourly,every_two_hours,every_three_hours,daily',
            'time' => 'string',
        ]);

        $value = $rule->value;
        if (isset($params['advertiser_id'])) {
            $value['advertiser_id'] = $params['advertiser_id'];
        }
        if (isset($params['all_campaigns'])) {
            $value['all_campaigns'] = $params['all_campaigns'];
        }
        if (isset($params['campaign_ids'])) {
            $value['campaign_ids'] = $params['campaign_ids'];
        }
        if (isset($params['rules'])) {
            $value['rules'] = $params['rules'];
        }
        if (isset($params['actions'])) {
            $value['actions'] = $params['actions'];
        }
        if (isset($params['time'])) {
            $value['time'] = $params['time'];
        }
        $params['value'] = $value;

        // TODO: Check campaign_ids, rules and actions

        $rule->update($params);

        return response([
            'message' => 'Rule created',
            'data' => $rule,
        ]);
    }

    /**
     * Delete rule
     *
     * @urlParam id int required Rule's id
     *
     * @response {
     *  "message": "Rule updated",
     *  "data": rule
     * }
     */
    public function deleteRule($id, Request $request)
    {
        // TODO: Authorization

        $rule = TikTokRule::findOrFail($id);
        $rule->delete();

        return response([
            'message' => 'Rule deleted',
        ]);
    }

    /**
     * Run rule
     *
     * @urlParam id int required Rule's id
     *
     * @response {
     *  "message": "Rule executed",
     * }
     */
    public function runRule($id, Request $request)
    {
        // TODO: Authorization

        $rule = TikTokRule::findOrFail($id);
        $rule->apply();

        return response([
            'message' => 'Rule executed',
        ]);
    }
}
