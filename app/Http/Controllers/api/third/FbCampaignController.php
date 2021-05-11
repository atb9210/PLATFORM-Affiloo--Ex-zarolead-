<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\third\FbCampaign as FbCampaign;
use App\Http\Resources\api\third\FbCampaignResource;

class FbCampaignController extends Controller
{
    /**
     * Get selected fb campaigns
     *
     * @urlParam fb_campaigns array Fb campaigns
     *
     * @response {
     *  "data": [fb_campaigns],
     * }
     */
    public function index(Request $request)
    {
        $params = $request->validate([
            'fb_campaigns' => 'array',
        ]);

        $fb_campaigns = FbCampaign::query();
        if (isset($params['fb_campaigns'])) {
            $fb_campaigns = $fb_campaigns->whereIn('id', $params['fb_campaigns']);
        }

        return FbCampaignResource::collection($fb_campaigns->get());
    }

    /**
     * Get fb campaign's informations.
     *
     * @urlParam id int required Fb campaign's id
     *
     * @response {
     *  "data": fb_campaign,
     * }
     */
    public function show($id)
    {
        $fb_campaign = FbCampaign::findOrFail($id);

        return new FbCampaignResource($fb_campaign);
    }

    /**
     * Delete the specified fb campaign.
     *
     * @urlParam id int required fb campaign's id
     *
     * @response {
     *  "message": "Fb campaign has been delete",
     * }
     */
    public function destroy($id)
    {
        $fb_campaign = FbCampaign::findOrFail($id);

        $fb_campaign->delete();

        return response([
            'message' => 'Fb campaign has been deleted'
        ]);
    }

    /**
     * Associate fb campaigns to a form.
     *
     * @bodyParam fb_campaigns int required Fb campaigns
     * @bodyParam form_id int required Form's id
     *
     * @response {
     *  "data": fb_campaign,
     * }
     */
    public function associate(Request $request)
    {
        $params = $request->validate([
            'fb_campaigns' => 'required|array',
            'form_id' => 'required|integer|exists:forms,id',
        ]);

        foreach ($params['fb_campaigns'] as $fb_campaign_id) {
            $fb_campaign = FbCampaign::find($fb_campaign_id);
            if ($fb_campaign === null) {
                $fb_campaign = FbCampaign::create([
                    'id' => $fb_campaign_id,
                    'form_id' => $params['form_id'],
                ]);
            } else {
                $fb_campaign->form_id = $params['form_id'];
                $fb_campaign->save();
            }
        }

        $fb_campaigns = FbCampaign::query();
        if (isset($params['fb_campaigns'])) {
            $fb_campaigns = $fb_campaigns->whereIn('id', $params['fb_campaigns']);
        }

        return FbCampaignResource::collection($fb_campaigns->get());
    }
}
