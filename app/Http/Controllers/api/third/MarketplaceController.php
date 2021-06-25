<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Driverfury\Worldfilia\Worldfilia;

class MarketplaceController extends Controller
{
    /**
     * Get informations of the user
     *
     * @response {
     *  "data": user,
     * }
     */
    public function me()
    {
        // TODO: Authorization

        $worldfilia = new Worldfilia(env('WORLDFILIA_API_TOKEN'));
        $user = $worldfilia->getUserInfo();
        $user['source_id'] = env('WORLDFILIA_SOURCE_ID');

        return response([
            'data' => $user,
        ]);
    }

    /**
     * Get campaigns list.
     *
     * @response {
     *  "data": [campaigns],
     * }
     */
    public function campaigns()
    {
        // TODO: Authorization

        $worldfilia = new Worldfilia(env('WORLDFILIA_API_TOKEN'));
        $campaigns = $worldfilia->getCampaigns();

        return response([
            'data' => $campaigns,
        ]);
    }

    /**
     * Get landings list of a specific campaign
     *
     * @urlParam id int required Campaign's id
     *
     * @response {
     *  "data": [landings],
     * }
     */
    public function landings($id)
    {
        // TODO: Authorization

        $worldfilia = new Worldfilia(env('WORLDFILIA_API_TOKEN'));
        $landings = $worldfilia->getLandings(intval($id));

        return response([
            'data' => $landings,
        ]);
    }

    /**
     * Get a product API JSON of a specific campaign
     *
     * @urlParam id int required Campaign's id
     *
     * @response {
     *  "data": product_json,
     * }
     */
    public function productJson($id)
    {
        // TODO: Authorization

        $worldfilia = new Worldfilia(env('WORLDFILIA_API_TOKEN'));
        $productJson = $worldfilia->getProductJson(
            intval($id),
            env('WORLDFILIA_SOURCE_ID'));

        return response([
            'data' => $productJson,
        ]);
    }
}
