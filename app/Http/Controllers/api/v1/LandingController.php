<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Landing as Landing;
use App\LandingStats as LandingStats;
use App\Form as Form;
use App\Http\Resources\api\v1\LandingResource;
use App\Http\Resources\api\v1\LandingStatsResource;

class LandingController extends Controller
{
    /**
     * Get all landings list.
     *
     * @urlParam filter_product int Order's product id
     *
     * @response {
     *  "data": [landings],
     * }
     */
    public function index(Request $request)
    {
        // Param validation
        $params = $request->validate([
            'filter_product' => 'integer|exists:products,id',
        ]);

        $landings = Landing::query();
        $landings = $landings->where('archived', 0);
        if (!Auth::user()->can('viewAny', 'App\Landing')) {
            $landings = $landings->where('user_id', Auth::user()->id);
        }

        if (isset($params['filter_product'])) {
            $landings = $landings->where('product_id', $params['filter_product']);
        }

        return LandingResource::collection($landings->get());
    }

    /**
     * Get landing's informations.
     *
     * @urlParam id int required Landing's id
     *
     * @response {
     *  "data": landing,
     * }
     */
    public function show($id)
    {
        $landing = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        if ($landing->archived) {
            return response([
                "message" => "That landing is archived"
            ], 403);
        }

        return new LandingResource($landing);
    }

    /**
     * Create a new landing.
     *
     * @bodyParam name string required Landing's name
     * @bodyParam product_id int required Landing's product id
     * @bodyParam url string required Landing's URL
     * @bodyParam country string Country
     *
     * @response 201 {
     *  "message": "Landing created",
     *  "data": [landing],
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Landing');

        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
            'product_id' => 'required|integer|exists:products,id',
            'url' => 'required|url',
            'country' => 'string|min:1',
        ]);
        $params['user_id'] = Auth::user()->id;

        $landing = Landing::create($params);

        return response([
            "message" => "Landing created",
            "data" => new LandingResource($landing),
        ], 201);
    }

    /**
     * Update the landing's fields.
     *
     * @urlParam id int required Landing's id
     *
     * @bodyParam name string Landing's name
     * @bodyParam product_id int Landing's product id
     * @bodyParam url string Landing's URL
     * @bodyParam country string Country
     *
     * @response {
     *  "data": landing,
     * }
     */
    public function update(Request $request, $id)
    {
        $landing = Landing::findOrFail($id);
        $this->authorize('update', $landing);

        if ($landing->archived) {
            return response([
                "message" => "That landing is archived"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'name' => 'string|min:3',
            'product_id' => 'integer|exists:products,id',
            'url' => 'url',
            'country' => 'string|min:1',
        ]);

        $landing->update($params);

        return new LandingResource($landing);
    }

    /**
     * Delete the specified landing.
     *
     * @urlParam id int required Landing's id
     *
     * @response {
     *  "message": "Landing has been archived",
     * }
     */
    public function destroy($id)
    {
        $landing = Landing::findOrFail($id);

        $this->authorize('delete', $landing);

        // Archive the landing
        $landing->archived = 1;
        $landing->save();

        return response([
            'message' => 'Landing has been archived'
        ]);
    }

    /**
     * Get landing stats.
     *
     * @urlParam id int required Landing's id
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     *
     * @response {
     *  "data": [stats]
     * }
     */
    public function stats($id, Request $request)
    {
        $landing = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $stats = LandingStats::where('landing_id', $id);
        if (isset($params['filter_date_from'])) {
            $stats = $stats->where('day', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $stats = $stats->where('day', '<=', $params['filter_date_to']);
        }
        $stats = $stats->orderBy('day', 'desc');

        return LandingStatsResource::collection($stats->get());
    }

    /**
     * Get form code
     *
     * @urlParam default_form int Default form's id
     *
     * @response {
     *  "data": code
     * }
     */
    public function code($id, Request $request)
    {
        // Param validation
        $params = $request->validate([
            'default_form' => 'required|integer|exists:forms,id',
        ]);
        $default_form = $params['default_form'];

        $landing = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        /*
        $form_html  = '<iframe scrolling="no" id="buy-frame" width="100%" height="500px" src="' . $form_url . '"></iframe>' . PHP_EOL;
        $form_html .= "<script type=\"text/javascript\">" . PHP_EOL;
        $form_html .= "document.addEventListener(\"DOMContentLoaded\", function(event) {" . PHP_EOL;
        $form_html .= "var loc = window.location.toString(), params = '', iframe_id = 'buy-frame';" . PHP_EOL;
        $form_html .= "if (loc.indexOf('?') > -1) {" . PHP_EOL;
        $form_html .= "params = loc.split('?')[1]," . PHP_EOL;
        $form_html .= "iframe = document.getElementById(iframe_id);" . PHP_EOL;
        $form_html .= "iframe_src_url = iframe.src;" . PHP_EOL;
        $form_html .= "separator = '?';" . PHP_EOL;
        $form_html .= "if (iframe_src_url.indexOf('?') > -1) {" . PHP_EOL;
        $form_html .= "separator = '&';" . PHP_EOL;
        $form_html .= "}" . PHP_EOL;
        $form_html .= "iframe.src = iframe_src_url + separator + params;" . PHP_EOL;
        $form_html .= "}" . PHP_EOL;
        $form_html .= "});" . PHP_EOL;
        $form_html .= "</script>" . PHP_EOL;
        */

        $form_url = "";
        if(env('APP_LOCAL', false)) {
            $form_url = "http://zarolead.localhost";
        } else if(env('APP_ALPHA', false)) {
            $form_url = "https://alpha.zarolead.com";
        } else if(env('APP_BETA', false)) {
            $form_url = "https://beta2.zarolead.com";
        }

        $form_html  = "<iframe scrolling=\"no\" id=\"buy-frame\" width=\"100%\" height=\"500px\" src=\"\"></iframe>" . PHP_EOL;
        $form_html .= "<script type=\"text/javascript\">" . PHP_EOL;
        $form_html .= "document.addEventListener('DOMContentLoaded', function(event) {" . PHP_EOL;
        $form_html .= "var url_string = window.location.toString();" . PHP_EOL;
        $form_html .= "var url = new URL(url_string);" . PHP_EOL;
        $form_html .= "var form_id = url.searchParams.get('id');" . PHP_EOL;
        $form_html .= "if (form_id == '' || form_id == null) { form_id = " . $default_form . "; }" . PHP_EOL;
        $form_html .= "var iframe_src = '" . $form_url . "/forms/' + form_id + '/get?landing=" . $id . "';" . PHP_EOL;

        $form_html .= "var loc = window.location.toString(), params = '';" . PHP_EOL;
        $form_html .= "if (loc.indexOf('?') > -1) {" . PHP_EOL;
        $form_html .= "    params = loc.split('?')[1]," . PHP_EOL;
        $form_html .= "    iframe_src_url = iframe_src;" . PHP_EOL;
        $form_html .= "    separator = '?';" . PHP_EOL;
        $form_html .= "    if (iframe_src_url.indexOf('?') > -1) {" . PHP_EOL;
        $form_html .= "        separator = '&';" . PHP_EOL;
        $form_html .= "    }" . PHP_EOL;
        $form_html .= "    iframe_src = iframe_src_url + separator + params;" . PHP_EOL;
        $form_html .= "}" . PHP_EOL;
        $form_html .= "" . PHP_EOL;
        $form_html .= "iframe_id = 'buy-frame';" . PHP_EOL;
        $form_html .= "iframe = document.getElementById(iframe_id);" . PHP_EOL;
        $form_html .= "iframe.src = iframe_src;" . PHP_EOL;
        $form_html .= "});" . PHP_EOL;
        $form_html .= "</script>" . PHP_EOL;

        return response([
            'data' => $form_html,
        ]);
    }
}
