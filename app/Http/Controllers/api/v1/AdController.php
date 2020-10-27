<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Ad as Ad;
use App\Http\Resources\api\v1\AdResource;
use App\TrafficSource as TrafficSource;

class AdController extends Controller
{
    /**
     * Get all ads list.
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     * @urlParam filter_traffic_source int Traffic source's id
     * @urlParam filter_product int Product's id
     *
     * @response {
     *  "data": [ads],
     * }
     */
    public function index(Request $request)
    {
        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
            'filter_traffic_source' => 'integer|exists:traffic_sources,id',
            'filter_product' => 'integer|exists:products,id',
        ]);

        $ads = Ad::query();
        if(!Auth::user()->can('viewAny', 'App\Ad')) {
            $ads = $ads->where('user_id', Auth::user()->id);
        }
        if (isset($params['filter_date_from'])) {
            $ads = $ads->where('created_at', '>=', $params['filter_date_from'] . " 00:00:00");
        }
        if (isset($params['filter_date_to'])) {
            $ads = $ads->where('created_at', '<=', $params['filter_date_to'] . " 23:59:59");
        }
        if (isset($params['filter_traffic_source'])) {
            $ads = $ads->where('traffic_source_id', $params['filter_traffic_source']);
        }
        if (isset($params['filter_product'])) {
            $ads = $ads->where('product_id', $params['filter_product']);
        }

        return AdResource::collection($ads->get());
    }

    /**
     * Get ad's informations.
     *
     * @urlParam id int required Ad's id
     *
     * @response {
     *  "data": ad,
     * }
     */
    public function show($id)
    {
        $ad = Ad::findOrFail($id);
        $this->authorize('view', $ad);
        return new AdResource($ad);
    }

    /**
     * Create a new ad.
     *
     * @bodyParam traffic_source_id int required Ad's traffic source id
     * @bodyParam product_id int required Ad's product id
     * @bodyParam ctr float required Ad's CTR
     * @bodyParam cpc float required Ad's CPC
     * @bodyParam cpa float required Ad's CPA
     * @bodyParam info json required Ad's info (title and text for example...)
     * @bodyParam product_media_id int required Ad's product media id
     *
     * @response 201 {
     *  "message": "Ad created",
     *  "data": ad,
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Ad');

        // Params validation
        $params = $request->validate([
            'traffic_source_id' => 'required|integer|exists:traffic_sources,id',
            'product_id' => 'required|integer|exists:products,id',
            'ctr' => 'required|numeric|min:0',
            'cpc' => 'required|numeric|min:0',
            'cpa' => 'required|numeric|min:0',
            'info' => 'required|json',
            'product_media_id' => 'required|integer|exists:product_medias,id',
        ]);
        $params['user_id'] = Auth::user()->id;

        // Validate info JSON
        $params['info'] = json_decode($params['info'], TRUE);
        $traffic_source = TrafficSource::findOrFail($params['traffic_source_id']);
        $rules = $traffic_source->getAdModel('validation');
        $validator = Validator::make($params['info'], $rules);
        if ($validator->fails()) {
            return response([
                'message' => 'The given data - for the info JSON param - was invalid.',
                'errors' => $validator->messages(),
            ], 422);
        }

        // Create Ad
        $ad = Ad::create($params);

        return response([
            "message" => "Ad created",
            "data" => new AdResource($ad),
        ], 201);
    }

    /**
     * Update the ad's fields.
     *
     * @urlParam id int required Ad's id
     *
     * @bodyParam product_id int Ad's product id
     * @bodyParam traffic_source_id int Ad's traffic source id
     * @bodyParam ctr float Ad's CTR
     * @bodyParam cpc float Ad's CPC
     * @bodyParam cpa float Ad's CPA
     * @bodyParam info json Ad's info (title and text for example...)
     * @bodyParam product_media_id int Ad's product media id
     *
     * @response {
     *  "data": ad,
     * }
     */
    public function update(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);
        $this->authorize('update', $ad);

        // Params validation
        $params = $request->validate([
            'traffic_source_id' => 'integer|exists:traffic_sources,id',
            'product_id' => 'integer|exists:products,id',
            'ctr' => 'numeric|min:0',
            'cpc' => 'numeric|min:0',
            'cpa' => 'numeric|min:0',
            'info' => 'json',
            'product_media_id' => 'required|integer|exists:product_medias,id',
        ]);

        // Validate info JSON
        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], TRUE);
            $traffic_source = TrafficSource::findOrFail($params['traffic_source_id']);
            $rules = $traffic_source->getAdModel('validation');
            $validator = Validator::make($params['info'], $rules);
            if ($validator->fails()) {
                return response([
                    'message' => 'The given data - for the info JSON param - was invalid.',
                    'errors' => $validator->messages(),
                ], 422);
            }
        }

        $ad->update($params);

        return response(new AdResource($ad));
    }

    /**
     * Delete the specified ad.
     *
     * @urlParam id int required Ad's id
     *
     * @response {
     *  "message": "Ad has been deleted",
     * }
     */
    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);

        $this->authorize('delete', $ad);

        // Delete the ad record
        $ad->delete();

        return response([
            'message' => 'Ad has been deleted'
        ]);
    }
}
