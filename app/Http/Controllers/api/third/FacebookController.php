<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// DEBUG
use Illuminate\Support\Facades\Log;

use Driverfury\Facebook\Facebook;

use App\third\FacebookLogin;

use App\third\FacebookRule;
use App\Http\Resources\api\third\FacebookRuleResource;

use App\third\FacebookAdvertiser;
use App\Http\Resources\api\third\FacebookAdvertiserResource;

use App\third\FacebookCopy;
use App\Http\Resources\api\third\FacebookCopyResource;

use App\third\FacebookMonitor;
use App\Http\Resources\api\third\FacebookMonitorResource;

use App\third\FacebookMonitorAction;

class FacebookController extends Controller
{
    /**
     * Get current access token
     *
     * @response {
     *  "access_token": access_token,
     * }
     */
    public function getToken(Request $request)
    {
        // TODO: Authorization
        
        $accessToken = FacebookLogin::getAccessToken();

        return response([
            'access_token' => $accessToken,
        ]);
    }

    /**
     * Remove current access token
     *
     * @response {
     *  "message": "Token has been removed",
     *  "access_token": access_token,
     * }
     */
    public function removeToken(Request $request)
    {
        // TODO: Authorization
        
        $accessToken = FacebookLogin::setAccessToken(null);

        return response([
            'message' => 'Token has been removed',
            'access_token' => $accessToken,
        ]);
    }

    /**
     * Get long lived access token
     *
     * @bodyParam access_token string required Access token // TODO DEBUG
     *
     * @response {
     *  "access_token": access_token,
     * }
     */
    public function refreshToken(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'access_token' => 'required|string|min:1',
        ]);

        $fb = new \Driverfury\Facebook\Facebook($params['access_token']);
        $newAccessToken = $fb->getLongLivedAccessToken(
            env('FACEBOOK_APP_ID'),
            env('FACEBOOK_APP_SECRET'));
        FacebookLogin::setAccessToken($newAccessToken);

        return response([
            'access_token' => $newAccessToken,
        ]);
    }

    /**
     * Send event
     *
     * @bodyParam access_token string required Access token // TODO DEBUG
     * @bodyParam test_code string required Test code // TODO DEBUG
     * @bodyParam pixel string required Pixel id
     * @bodyParam event string required Event name
     * @bodyParam event_source_url string Event source URL
     * @bodyParam custom_data json Custom data
     *
     * @response {
     *  "status": "success",
     *  "message": "Event has been sent",
     * }
     */
    public function sendEvent(Request $request)
    {
        $events = [
            'AddPaymentInfo',
            'AddToCart',
            'AddToWishlist',
            'CompleteRegistration',
            'Contact',
            'CustomizeProduct',
            'Donate',
            'InitiateCheckout',
            'Lead',
            'PageView',
            'Purchase',
            'Schedule',
            'Search',
            'StartTrial',
            'SubmitApplication',
            'Subscribe',
            'ViewContent',
        ];
        $eventsList = implode(',', $events);

        // Param validation
        $params = $request->validate([
            'access_token' => 'required|string|min:1', // TODO DEBUG
            'test_code' => 'required|string|min:1', // TODO DEBUG
            'pixel' => 'required|string|min:1',
            'event' => 'required|string|min:1|in:' . $eventsList,
            'event_source_url' => 'string',
            'fbc' => 'nullable|string',
            'custom_data' => 'json',
        ]);

        // TODO DEBUG: What about token?
        //$token = env('FACEBOOK_API_KEY');
        $token = $params['access_token'];

        // TODO DEBUG
        $testCode = $params['test_code'];

        $pixel = $params['pixel'];
        $event = $params['event'];
        $userData = [
            'client_ip_address' => request()->ip(),
            'client_user_agent' => request()->server('HTTP_USER_AGENT'),
        ];
        if (isset($params['fbc']) && $params['fbc'] !== null && $params['fbc'] != '') {
            $fbc = 'fb.1.' . strval(time()) . '.' . $params['fbc'];
            $userData['fbc'] = $fbc;
        }
        $eventSourceUrl = $_SERVER['HTTP_REFERER'];
        if (isset($params['event_source_url'])) {
            $eventSourceUrl = $params['event_source_url'];
        }
        $customData = [];
        if (isset($params['custom_data'])) {
            $customData = json_decode($params['custom_data'], true);
        }

        // DEBUG: TODO: to delete
        Log::channel('facebook')->info([
            'event' => $event,
            'userData' => $userData,
            'customData' => $customData,
        ]);

        $fb = new \Driverfury\Facebook\Facebook($token);

        // TODO DEBUG
        $fb->sendEventTest($pixel, $testCode, $event, $userData, $eventSourceUrl, $customData);

        return response([
            'status' => 'success',
            'message' => 'Event has been sent',
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

        $rules = FacebookRule::all();

        return FacebookRuleResource::collection($rules);
    }

    /**
     * Create rule
     *
     * @bodyParam name string required Rule's name
     * @bodyParam access_token string required Access token
     * @bodyParam ad_account_id string required Ad account's id
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
            'access_token' => 'string|required',
            'ad_account_id' => 'string|required',
            'all_campaigns' => 'boolean',
            'campaign_ids' => 'array|required',
            'rules' => 'json|required',
            'actions' => 'json|required',
            'status' => 'in:active,disabled',
            'frequency' => 'required|in:every_five_minutes,every_ten_minutes,every_fifteen_minutes,every_thirty_minutes,hourly,every_two_hours,every_three_hours,daily',
            'time' => 'string',
        ]);

        $access_token = $params['access_token'];
        $ad_account_id = $params['ad_account_id'];
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

        $rule = FacebookRule::create([
            'name' => $params['name'],
            'value' => [
                'access_token' => $access_token,
                'ad_account_id' => $ad_account_id,
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
     * @bodyParam access_token string Access token
     * @bodyParam ad_account_id string Ad account's id
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

        $rule = FacebookRule::findOrFail($id);

        // Param validation
        $params = $request->validate([
            'name' => 'string',
            'access_token' => 'string',
            'ad_account_id' => 'string',
            'all_campaigns' => 'boolean',
            'campaign_ids' => 'array',
            'rules' => 'json',
            'actions' => 'json',
            'status' => 'in:active,disabled',
            'frequency' => 'in:every_five_minutes,every_ten_minutes,every_fifteen_minutes,every_thirty_minutes,hourly,every_two_hours,every_three_hours,daily',
            'time' => 'string',
        ]);

        $value = $rule->value;
        if (isset($params['access_token'])) {
            $value['access_token'] = $params['access_token'];
        }
        if (isset($params['ad_account_id'])) {
            $value['ad_account_id'] = $params['ad_account_id'];
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

        $rule = FacebookRule::findOrFail($id);
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

        $rule = FacebookRule::findOrFail($id);
        $rule->apply();

        return response([
            'message' => 'Rule executed',
        ]);
    }

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

        $advertisers = FacebookAdvertiser::all();
        return FacebookAdvertiserResource::collection($advertisers);
    }

    /**
     * Get advertiser
     *
     * @response {
     *  "data": advertiser,
     * }
     */
    public function getAdvertiser($id, Request $request)
    {
        // TODO: Authorization

        $advertiser = FacebookAdvertiser::findOrFail($id);
        return new FacebookAdvertiserResource($advertiser);
    }

    /**
     * Create advertiser
     *
     * @bodyParam name string Advertiser's name
     * @bodyParam access_token string required Advertiser's access token
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
            'name' => 'nullable|string',
            'access_token' => 'required|string|min:32',
        ]);

        $accessToken = $params['access_token'];
        if (!isset($params['name']) || $params['name'] === null) {
            $fb = new \Driverfury\Facebook\Facebook($accessToken);
            $info = $fb->getMe(['id', 'name']);
            if ($info !== null && isset($info['name'])) {
                $params['name'] = $info['name'];
            } else {
                return response([
                    'message' => 'Invalid access token',
                    'errors' => [
                        'access_token' => [ 'Invalid access token' ],
                    ],
                ], 422);
            }
        }

        $advertiser = FacebookAdvertiser::create($params);
        return response([
            'message' => 'Facebook Advertiser has been successfully created',
            'data' => new FacebookAdvertiserResource($advertiser),
        ]);
    }

    /**
     * Update advertiser
     *
     * @urlParam id string required Advertiser's id
     *
     * @bodyParam name string Advertiser's new name
     * @bodyParam access_token string Advertiser's new access token
     *
     * @response {
     *  "data": advertiser,
     * }
     */
    public function updateAdvertiser($id, Request $request)
    {
        // TODO: Authorization

        $advertiser = FacebookAdvertiser::findOrFail($id);

        // Param validation
        $params = $request->validate([
            'name' => 'string|min:1',
            'access_token' => 'string|min:32',
        ]);

        $accessToken = $params['access_token'];
        if (!isset($params['name']) || $params['name'] === null) {
            $fb = new \Driverfury\Facebook\Facebook($accessToken);
            $info = $fb->getMe(['id', 'name']);
            if ($info !== null && isset($info['name'])) {
                $params['name'] = $info['name'];
            } else {
                return response([
                    'message' => 'Invalid access token',
                    'errors' => [
                        'access_token' => [ 'Invalid access token' ],
                    ],
                ], 422);
            }
        }

        $advertiser->update($params);

        return response([
            'data' => new FacebookAdvertiserResource($advertiser),
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

        $advertiser = FacebookAdvertiser::findOrFail($id);
        $advertiser->delete();
        return response([
            'message' => 'Advertiser deleted',
        ]);
    }

    /**
     * Get ad accounts list
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam reports bool Reports (default: true)
     * @bodyParam filter_date_from string From date (Y-m-d format)
     * @bodyParam filter_date_to string To date (Y-m-d format)
     *
     * @response {
     *  "data": [adaccounts],
     * }
     */
    public function adAccounts(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'advertiser_id' => 'required|exists:third_facebook_advertisers,id',
            'reports' => 'nullable|boolean',
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $reports = [];
        if (!isset($params['reports']) || boolval($params['reports']) === true) {
            $reports = [
                'metrics' => [
                    'spend',
                    'cpm',
                    'actions',
                ],
            ];
        }

        if (isset($params['filter_date_from'])) {
            $reports['start_date'] = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $reports['end_date'] = $params['filter_date_to'];
        }

        $advertiser = FacebookAdvertiser::findOrFail($params['advertiser_id']);
        $fb = new \Driverfury\Facebook\Facebook($advertiser->access_token);
        $adAccounts = $fb->getAdAccounts($reports);
        return response([
            'data' => $adAccounts,
        ]);
    }

    /**
     * Get campaigns list
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam parent_id string required Parent's id
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
            'advertiser_id' => 'required|exists:third_facebook_advertisers,id',
            'parent_id' => 'nullable|string|min:4',
            'reports' => 'nullable|boolean',
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $reports = [];
        if (!isset($params['reports']) || boolval($params['reports']) === true) {
            $reports = [
                'metrics' => [
                    'spend',
                    'cpm',
                    'actions',
                ],
            ];
        }

        if (isset($params['filter_date_from'])) {
            $reports['start_date'] = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $reports['end_date'] = $params['filter_date_to'];
        }

        $advertiser = FacebookAdvertiser::findOrFail($params['advertiser_id']);
        $fb = new \Driverfury\Facebook\Facebook($advertiser->access_token);
        $campaigns = [];
        if (isset($params['parent_id']) && $params['parent_id'] !== null) {
            $campaigns = $fb->getCampaigns($params['parent_id'], $reports);
        } else {
            // TODO: Get all campaigns of the specified advertiser (all ad accounts)
        }

        return response([
            'data' => $campaigns,
        ]);
    }
    
    /**
     * Get adsets list
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam parent_id string required Parent's id
     * @bodyParam reports bool Reports (default: true)
     * @bodyParam filter_date_from string From date (Y-m-d format)
     * @bodyParam filter_date_to string To date (Y-m-d format)
     *
     * @response {
     *  "data": [adsets],
     * }
     */
    public function adsets(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'advertiser_id' => 'required|exists:third_facebook_advertisers,id',
            'parent_id' => 'nullable|string|min:4',
            'reports' => 'nullable|boolean',
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $reports = [];
        if (!isset($params['reports']) || boolval($params['reports']) === true) {
            $reports = [
                'metrics' => [
                    'spend',
                    'cpm',
                    'actions',
                ],
            ];
        }

        if (isset($params['filter_date_from'])) {
            $reports['start_date'] = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $reports['end_date'] = $params['filter_date_to'];
        }

        $advertiser = FacebookAdvertiser::findOrFail($params['advertiser_id']);
        $fb = new \Driverfury\Facebook\Facebook($advertiser->access_token);
        $adsets = [];
        if (isset($params['parent_id']) && $params['parent_id'] !== null) {
            $adsets = $fb->getAdsets($params['parent_id'], $reports);
        } else {
            // TODO: Get all adsets of the specified advertiser (all ad accounts)
        }

        return response([
            'data' => $adsets,
        ]);
    }

    /**
     * Get ads list
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam parent_id string required Parent's id
     * @bodyParam reports bool Reports (default: true)
     * @bodyParam filter_date_from string From date (Y-m-d format)
     * @bodyParam filter_date_to string To date (Y-m-d format)
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
            'advertiser_id' => 'required|exists:third_facebook_advertisers,id',
            'parent_id' => 'nullable|string|min:4',
            'reports' => 'nullable|boolean',
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $reports = [];
        if (!isset($params['reports']) || boolval($params['reports']) === true) {
            $reports = [
                'metrics' => [
                    'spend',
                    'cpm',
                    'actions',
                ],
            ];
        }

        if (isset($params['filter_date_from'])) {
            $reports['start_date'] = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $reports['end_date'] = $params['filter_date_to'];
        }

        $advertiser = FacebookAdvertiser::findOrFail($params['advertiser_id']);
        $fb = new \Driverfury\Facebook\Facebook($advertiser->access_token);
        $ads = [];
        if (isset($params['parent_id']) && $params['parent_id'] !== null) {
            $ads = $fb->getAds($params['parent_id'], $reports);
        } else {
            // TODO: Get all ads of the specified advertiser (all ad accounts)
        }

        return response([
            'data' => $ads,
        ]);
    }

    /**
     * Get copys list
     *
     * @bodyParam filter_product int Product's id
     *
     * @response {
     *  "data": [copys],
     * }
     */
    public function getCopys(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'filter_product' => 'numeric|exists:products,id',
        ]);
        $copys = FacebookCopy::query();
        if (isset($params['filter_product'])) {
            $copys = $copys->where('product_id', $params['filter_product']);
        }
        $copys = $copys->get();
        return FacebookCopyResource::collection($copys);
    }

    /**
     * Get copy
     *
     * @response {
     *  "data": copy,
     * }
     */
    public function getCopy($id, Request $request)
    {
        // TODO: Authorization
        $copy = FacebookCopy::findOrFail($id);
        return new FacebookCopyResource($copy);
    }

    /**
     * Create copy
     *
     * @bodyParam ad_title string required Ad's title
     * @bodyParam ad_text  string required Ad's text
     *
     * @response {
     *  "data": copy,
     * }
     */
    public function createCopy(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'ad_title'   => 'required|string|min:1',
            'ad_text'    => 'required|string|min:1',
            'product_id' => 'required|numeric|exists:products,id',
        ]);

        $number = 0;
        $copys = FacebookCopy::where('product_id', $params['product_id'])->get();
        for ($copyIndex = 0; $copyIndex < count($copys); $copyIndex++) {
            $num = intval($copys[$copyIndex]->number);
            if ($num > $number) {
                $number = $num;
            }
        }
        $number += 1;
        $params['number'] = $number;

        $copy = FacebookCopy::create($params);
        return new FacebookCopyResource($copy);
    }

    /**
     * Update copy
     *
     * @urlParam id int required Copy's id
     *
     * @bodyParam ad_title string Ad's title
     * @bodyParam ad_text  string Ad's text
     *
     * @response {
     *  "data": copy,
     * }
     */
    public function updateCopy($id, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'ad_title'   => 'string|min:1',
            'ad_text'    => 'string|min:1',
            'product_id' => 'numeric|exists:products,id',
        ]);

        $copy = FacebookCopy::findOrFail($id);
        $copy->update($params);

        return new FacebookCopyResource($copy);
    }

    /**
     * Delete copy
     *
     * @urlParam id int required Copy's id
     *
     * @response {
     *  "message": "Copy deleted",
     * }
     */
    public function deleteCopy($id, Request $request)
    {
        // TODO: Authorization

        $copy = FacebookCopy::findOrFail($id);
        $copy->delete();

        return response([
            'message' => 'Copy deleted',
        ]);
    }

    /**
     * Get monitors list
     *
     * @bodyParam filter_advertiser int Advertiser's id
     * @bodyParam filter_form int Form's id
     * @bodyParam filter_date_from int From date
     * @bodyParam filter_date_to int To date
     * @bodyParam reports boolean To date
     *
     * @response {
     *  "data": [monitor],
     * }
     */
    public function getMonitors(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'filter_advertiser' => 'numeric|exists:third_facebook_advertisers,id',
            'filter_form'       => 'numeric|exists:forms,id',
            'filter_date_from'  => 'date|date_format:Y-m-d',
            'filter_date_to'    => 'date|date_format:Y-m-d',
            'reports'           => 'boolean',
        ]);
        $monitors = FacebookMonitor::query();
        if (isset($params['filter_advertiser'])) {
            $monitors = $monitors->where('advertiser_id', $params['filter_advertiser']);
        }
        if (isset($params['filter_form'])) {
            $monitors = $monitors->where('form_id', $params['filter_form']);
        }
        $monitors = $monitors->get();

        return FacebookMonitorResource::collection($monitors);
    }

    /**
     * Get monitor
     *
     * @response {
     *  "data": monitor,
     * }
     */
    public function getMonitor($id, Request $request)
    {
        // TODO: Authorization
        $monitor = FacebookMonitor::findOrFail($id);
        return new FacebookMonitorResource($monitor);
    }

    /**
     * Create monitor
     *
     * @bodyParam name string required Name
     * @bodyParam advertiser_id int required Facebook advertiser's id
     * @bodyParam form_id int required Form's id
     * @bodyParam campaigns json required Campaigns
     * @bodyParam strategy json Campaigns
     *
     * @response {
     *  "data": monitor,
     * }
     */
    public function createMonitor(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'name'          => 'required|string|min:1|unique:third_facebook_monitors,name',
            'advertiser_id' => 'required|numeric|exists:third_facebook_advertisers,id',
            'form_id'       => 'required|numeric|exists:forms,id',
            'campaigns'     => 'required|json',
            'strategy'      => 'json',
        ]);
        $params['campaigns'] = json_decode($params['campaigns'], true);
        if (isset($params['strategy'])) {
            $params['strategy'] = json_decode($params['strategy'], true);
        }

        // TODO: Check campaigns field?

        $monitor = FacebookMonitor::create($params);
        return new FacebookMonitorResource($monitor);
    }

    /**
     * Update monitor
     *
     * @urlParam id int required Monitor's id
     *
     * @bodyParam name string Name
     * @bodyParam advertiser_id int Facebook advertiser's id
     * @bodyParam form_id int Form's id
     * @bodyParam campaigns json Campaigns
     * @bodyParam strategy json Campaigns
     *
     * @response {
     *  "data": monitor,
     * }
     */
    public function updateMonitor($id, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'name'          => 'string|min:1|unique:third_facebook_monitors,name',
            'advertiser_id' => 'numeric|exists:third_facebook_advertisers,id',
            'form_id'       => 'numeric|exists:forms,id',
            'campaigns'     => 'json',
            'strategy'      => 'json',
        ]);

        if (isset($params['campaigns'])) {
            $params['campaigns'] = json_decode($params['campaigns'], true);
        }
        if (isset($params['strategy'])) {
            $params['strategy'] = json_decode($params['strategy'], true);
        }

        $monitor = FacebookMonitor::findOrFail($id);
        $monitor->update($params);

        return new FacebookMonitorResource($monitor);
    }

    /**
     * Delete monitor
     *
     * @urlParam id int required Monitor's id
     *
     * @response {
     *  "message": "Monitor deleted",
     * }
     */
    public function deleteMonitor($id, Request $request)
    {
        // TODO: Authorization

        $monitor = FacebookMonitor::findOrFail($id);
        $monitor->delete();

        return response([
            'message' => 'Monitor deleted',
        ]);
    }

    /**
     * Get monitor's actions list
     *
     * @urlParam id int required Monitor's id
     *
     * @bodyParam filter_date_from int From date
     * @bodyParam filter_date_to int To date
     * @bodyParam filter_action_type string Action type
     * @bodyParam filter_campaign string Campaign's id
     *
     * @response {
     *  "data": [actions],
     * }
     */
    public function getMonitorActions($id, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'filter_date_from'   => 'date|date_format:Y-m-d',
            'filter_date_to'     => 'date|date_format:Y-m-d',
            'filter_action_type' => 'string',
            'filter_campaign'    => 'string',
        ]);
        $actions = FacebookMonitorAction::query();
        $actions = $actions->where('monitor_id', $id);
        if (isset($params['filter_date_from'])) {
            $actions = $actions->where('created_at', '>=', $params['filter_date_from'] . ' 00:00:00');
        }
        if (isset($params['filter_date_to'])) {
            $actions = $actions->where('created_at', '<=', $params['filter_date_to'] . ' 23:59:59');
        }
        if (isset($params['filter_action_type'])) {
            $actions = $actions->where('action_type', $params['filter_action_type']);
        }
        if (isset($params['filter_campaign'])) {
            $actions = $actions->where('campaign_id', $params['filter_campaign']);
        }
        $actions = $actions->get();

        return response([
            'data' => $actions,
        ]);
    }

}
