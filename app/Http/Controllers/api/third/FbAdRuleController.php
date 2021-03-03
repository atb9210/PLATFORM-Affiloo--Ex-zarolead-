<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\third\FbAdRule as FbAdRule;
use App\Http\Resources\api\third\FbAdRuleResource;

class FbAdRuleController extends Controller
{
    /**
     * Get all fb ad rules list.
     *
     * @response {
     *  "data": [fb_ad_rules],
     * }
     */
    public function index(Request $request)
    {
        $fb_ad_rules = FbAdRule::query();
        return FbAdRuleResource::collection($fb_ad_rules->get());
    }

    /**
     * Get fb ad rule's informations.
     *
     * @urlParam id int required Fb ad rule's id
     *
     * @response {
     *  "data": fb_ad_rule,
     * }
     */
    public function show($id)
    {
        $fb_ad_rule = FbAdRule::findOrFail($id);
        return new FbAdRuleResource($fb_ad_rule);
    }

    /**
     * Create a new fb ad rule.
     *
     * @bodyParam name string required Fb ad rule's name
     * @bodyParam value json required Fb ad rule's value
     *
     * @response 201 {
     *  "message": "Fb ad rule created",
     *  "data": fb_ad_rule,
     * }
     */
    public function store(Request $request)
    {
        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:1',
            'value' => 'required|json',
        ]);
        if (isset($params['value'])) {
            $params['value'] = json_decode($params['value'], true);
        }

        $fb_ad_rule = FbAdRule::create($params);

        return response([
            "message" => "Fb ad rule created",
            "data" => new FbAdRuleResource($fb_ad_rule),
        ], 201);
    }

    /**
     * Update the fb ad rule's fields.
     *
     * @urlParam id int required Fb ad rule's id
     *
     * @bodyParam name string Fb ad rule's name
     * @bodyParam value json Fb ad rule's value
     *
     * @response {
     *  "data": fb_ad_rule,
     * }
     */
    public function update(Request $request, $id)
    {
        $fb_ad_rule = FbAdRule::findOrFail($id);

        // Params validation
        $params = $request->validate([
            'name' => 'string|min:1',
            'value' => 'nullable|json',
        ]);
        if (isset($params['value'])) {
            $params['value'] = json_decode($params['value'], true);
        }

        $fb_ad_rule->update($params);

        return new FbAdRuleResource($fb_ad_rule);
    }

    /**
     * Delete the specified fb ad rule.
     *
     * @urlParam id int required fb ad rule's id
     *
     * @response {
     *  "message": "Fb ad rule has been delete",
     * }
     */
    public function destroy($id)
    {
        $fb_ad_rule = FbAdRule::findOrFail($id);
        $fb_ad_rule->delete();
        return response([
            'message' => 'Fb ad rule has been deleted'
        ]);
    }
}
