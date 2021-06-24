<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Driverfury\Worldfilia\Worldfilia;

class MarketplaceController extends Controller
{
    /**
     * Get campaigns list.
     *
     * @response {
     *  "data": [tags],
     * }
     */
    public function campaigns()
    {
        // TODO: Authorization

        $worldfilia = new Worldfilia(env('WORLDFILIA_API_KEY'));
        $campaigns = $worldfilia->getCampaigns();

        return response([
            'data' => $campaigns,
        ]);
    }

    /**
     * Get landings list of a specific $campaign
     *
     * @response {
     *  "data": [tags],
     * }
     */
    public function landings($id)
    {
        // TODO: Authorization

        $worldfilia = new Worldfilia(env('WORLDFILIA_API_KEY'));
        $landings = $worldfilia->getLandings(intval($id));

        return response([
            'data' => $landings,
        ]);
    }
}
