<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// DEBUG
use Illuminate\Support\Facades\Log;

use Driverfury\Facebook\Facebook;
use App\Http\Resources\api\third\FacebookRuleResource;

use App\third\FacebookAdvertiser;
use App\Http\Resources\api\third\FacebookAdvertiserResource;

use App\third\FacebookLogin;

class FacebookController extends Controller
{
    // TODO: Put this inside .env
    private $fbAppId = '1031757410984629';
    private $fbAppSecret = '766d936bd8980c34b4947960e479a9be';
    private $fbApiVersion = 'v12.0';

    private function fbClient()
    {
        return new \Facebook\Facebook([
            'app_id' => $this->fbAppId,
            'app_secret' => $this->fbAppSecret,
            'default_graph_version' => $this->fbApiVersion,
        ]);
    }

    public function login(Request $request)
    {
        session_start();
        $fb = $this->fbClient();
        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email'];
        $loginUrl = $helper->getLoginUrl(env('APP_URL') . '/facebook/callback', $permissions);
        return redirect($loginUrl);
    }

    public function callback(Request $request)
    {
        session_start();
        $fb = $this->fbClient();
        $helper = $fb->getRedirectLoginHelper();
        $accessToken = $helper->getAccessToken();
        $oAuth2Client = $fb->getOAuth2Client();
        if (!$accessToken->isLongLived()) {
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        }
        $params = [
            'user_id' => Auth::user()->id,
            'access_token' => $accessToken->getValue(),
            'expires_at' => $accessToken->getExpiresAt(),
        ];
        $fbLogin = FacebookLogin::getCurrent();
        if ($fbLogin === null) {
            FacebookLogin::create($params);
        } else {
            FacebookLogin::update($params);
        }
        return redirect('/');
    }

    public function me(Request $request)
    {
        $fb = $this->fbClient();
        echo 'ho';
        exit();
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
     * Create advertiser
     *
     * @bodyParam id string required Advertiser's id
     * @bodyParam name string required Advertiser's name
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
            'id' => 'required|string|unique:third_facebook_advertisers,id',
            'name' => 'required|string|min:1',
            'access_token' => 'required|string|min:1',
        ]);

        $advertiser = FacebookAdvertiser::create($params);
        return response([
            'message' => 'Advertiser has been successfully created',
            'data' => new FacebookAdvertiserResource($advertiser),
        ]);
    }

    /**
     * Update advertiser
     *
     * @urlParam id string required Advertiser's id
     *
     * @bodyParam id string Advertiser's new id
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
            'id' => 'string|unique:third_facebook_advertisers,id,' . $advertiser->id,
            'name' => 'string|min:1',
            'access_token' => 'string|min:1',
        ]);

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
            'advertiser_id' => 'required|exists:third_facebook_advertisers,id',
            'reports' => 'nullable|boolean',
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $reports = [
            'metrics' => [
                'bid',
                'budget',
                'result',
                'spent',
                'impressions',
                'clicks',
                'cpm',
                'ctr',
                'value',
            ],
        ];

        if (isset($params['filter_date_from'])) {
            $reports['start_date'] = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $reports['end_date'] = $params['filter_date_to'];
        }

        $advertiser = FacebookAdvertiser::findOrFail($params['advertiser_id']);
        $fb = new Facebook($advertiser->access_token);
        $campaigns = $fb->getCampaigns($params['advertiser_id'], $reports);

        return response([
            'data' => $campaigns,
        ]);
    }

    /**
     * Get adsets list
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam filter_campaign integer required Campaign (parent) id
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
            'filter_campaign' => 'required|numeric|min:1',
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $reports = [
            'metrics' => [
                'bid',
                'budget',
                'result',
                'spent',
                'impressions',
                'clicks',
                'cpm',
                'ctr',
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

        $advertiser = FacebookAdvertiser::findOrFail($params['advertiser_id']);
        $fb = new Facebook($advertiser->access_token);
        $adsets = $fb->getAdsets($parentId, $reports);

        return response([
            'data' => $adsets,
        ]);
    }

    /**
     * Get ads list
     *
     * @bodyParam advertiser_id string required Advertiser's id
     * @bodyParam filter_adset integer required Adset (parent) id
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
            'filter_adset' => 'required|numeric|min:1',
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $reports = [
            'metrics' => [
                'bid',
                'budget',
                'result',
                'spent',
                'impressions',
                'clicks',
                'cpm',
                'ctr',
            ],
        ];

        if (isset($params['filter_date_from'])) {
            $reports['start_date'] = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $reports['end_date'] = $params['filter_date_to'];
        }
        $parentId = '';
        if (isset($params['filter_adset'])) {
            $parentId = strval($params['filter_adset']);
        }

        $advertiser = FacebookAdvertiser::findOrFail($params['advertiser_id']);
        $fb = new Facebook($advertiser->access_token);
        $ads = $fb->getAds($parentId, $reports);

        return response([
            'data' => $ads,
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
        $eventSourceUrl = $_SERVER['HTTP_REFERER'];
        if (isset($params['event_source_url'])) {
            $eventSourceUrl = $params['event_source_url'];
        }
        $customData = [];
        if (isset($params['custom_data'])) {
            $customData = json_decode($params['custom_data'], true);
        }

        $fb = new \Driverfury\Facebook\Facebook($token);

        // TODO DEBUG
        $fb->sendEventTest($pixel, $testCode, $event, $userData, $eventSourceUrl, $customData);

        return response([
            'message' => 'Event has been sent',
        ]);
    }
}
