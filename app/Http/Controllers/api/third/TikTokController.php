<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Driverfury\TikTok\TikTok;

class TikTokController extends Controller
{
    /**
     * Get campaigns list
     *
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
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $reports = [
            'metrics' => ['spend', 'conversion', 'cost_per_conversion', 'conversion_rate', 'cpm', 'impressions'],
        ];

        if (isset($params['filter_date_from'])) {
            $reports['start_date'] = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $reports['end_date'] = $params['filter_date_to'];
        }

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        $campaigns = $tt->getCampaigns(env('TIKTOK_ADVERTISER_ID'), $reports);

        return response([
            'data' => $campaigns,
        ]);
    }

    /**
     * Get adgroups list
     *
     * @bodyParam filter_date_from string From date (Y-m-d format)
     * @bodyParam filter_date_to string To date (Y-m-d format)
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
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $reports = [
            'metrics' => ['spend', 'result', 'cost_per_conversion', 'conversion_rate', 'cpm', 'impressions'],
        ];

        if (isset($params['filter_date_from'])) {
            $reports['start_date'] = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $reports['end_date'] = $params['filter_date_to'];
        }

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        $adgroups = $tt->getAdgroups(env('TIKTOK_ADVERTISER_ID'), $reports);

        return response([
            'data' => $adgroups,
        ]);
    }

    /**
     * Get ads list
     *
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
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $reports = [
            'metrics' => ['spend', 'result', 'cost_per_conversion', 'conversion_rate', 'cpm', 'impressions'],
        ];

        if (isset($params['filter_date_from'])) {
            $reports['start_date'] = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $reports['end_date'] = $params['filter_date_to'];
        }

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        $ads = $tt->getAds(env('TIKTOK_ADVERTISER_ID'), $reports);

        return response([
            'data' => $ads,
        ]);
    }

    /**
     * Update campaign
     *
     * @urlParam id string required Campaign id
     *
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
            'status' => 'in:DELETE,DISABLE,ENABLE',
        ]);

        $success = true;

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        if ($success && isset($params['status'])) {
            if(!$tt->updateCampaignStatus(
                env('TIKTOK_ADVERTISER_ID'),
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
            'status' => 'in:DELETE,DISABLE,ENABLE',
            'budget' => 'numeric|min:0',
            'bid_strategy' => 'string|in:BID_TYPE_CUSTOM,BID_TYPE_MAX_CONVERSION,BID_TYPE_NO_BID',
            'bid' => 'numeric|min:0',
        ]);

        $success = true;

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        if ($success && isset($params['status'])) {
            if(!$tt->updateAdgroupStatus(
                env('TIKTOK_ADVERTISER_ID'),
                $id, $params['status'])) {
                $success = false;
            }
        }
        if ($success && isset($params['budget'])) {
            $params['budget'] = floatval($params['budget']);
            if(!$tt->updateAdgroupBudget(
                env('TIKTOK_ADVERTISER_ID'),
                $id, $params['budget'])) {
                $success = false;
            }
        }
        if ($success && isset($params['bid']) && isset($params['bid_strategy'])) {
            if(!$tt->updateAdgroupBid(
                env('TIKTOK_ADVERTISER_ID'), $id,
                $params['bid_strategy'], floatval($params['status']))) {
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
            'status' => 'in:DELETE,DISABLE,ENABLE',
        ]);

        $success = true;

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        if($success && isset($params['status'])) {
            if(!$tt->updateAdStatus(
                env('TIKTOK_ADVERTISER_ID'),
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
            'image_id' => 'required|string|min:1',
        ]);

        $tt = new TikTok(env('TIKTOK_API_KEY'));
        $image = $tt->getImageInfo(
            env('TIKTOK_ADVERTISER_ID'),
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
}
