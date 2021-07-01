<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\third\FbAdTemplate as FbAdTemplate;
use App\Http\Resources\api\third\FbAdTemplateResource;

class FbAdTemplateController extends Controller
{
    /**
     * Get all fb ad templates list.
     *
     * @response {
     *  "data": [fb_ad_templates],
     * }
     */
    public function index(Request $request)
    {
        $fb_ad_templates = FbAdTemplate::query();
        return FbAdTemplateResource::collection($fb_ad_templates->get());
    }

    /**
     * Get fb ad template's informations.
     *
     * @urlParam id int required Fb ad template's id
     *
     * @response {
     *  "data": fb_ad_template,
     * }
     */
    public function show($id)
    {
        $fb_ad_template = FbAdTemplate::findOrFail($id);
        return new FbAdTemplateResource($fb_ad_template);
    }

    /**
     * Create a new fb ad template.
     *
     * @bodyParam name string required Fb ad template's name
     * @bodyParam value json required Fb ad template's value
     *
     * @response 201 {
     *  "message": "Fb ad template created",
     *  "data": fb_ad_template,
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

        $fb_ad_template = FbAdTemplate::create($params);

        return response([
            "message" => "Fb ad template created",
            "data" => new FbAdTemplateResource($fb_ad_template),
        ], 201);
    }

    /**
     * Update the fb ad template's fields.
     *
     * @urlParam id int required Fb ad template's id
     *
     * @bodyParam name string Fb ad template's name
     * @bodyParam value json Fb ad template's value
     *
     * @response {
     *  "data": fb_ad_template,
     * }
     */
    public function update(Request $request, $id)
    {
        $fb_ad_template = FbAdTemplate::findOrFail($id);

        // Params validation
        $params = $request->validate([
            'name' => 'string|min:1',
            'value' => 'nullable|json',
        ]);
        if (isset($params['value'])) {
            $params['value'] = json_decode($params['value'], true);
        }

        $fb_ad_template->update($params);

        return new FbAdTemplateResource($fb_ad_template);
    }

    /**
     * Delete the specified fb ad template.
     *
     * @urlParam id int required fb ad template's id
     *
     * @response {
     *  "message": "Fb ad template has been delete",
     * }
     */
    public function destroy($id)
    {
        $fb_ad_template = FbAdTemplate::findOrFail($id);
        $fb_ad_template->delete();
        return response([
            'message' => 'Fb ad template has been deleted'
        ]);
    }
}
