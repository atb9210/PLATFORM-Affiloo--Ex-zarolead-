<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Form as Form;
use App\Http\Resources\api\v1\FormResource;
use App\FormStats as FormStats;
use App\Http\Resources\api\v1\FormStatsResource;
use App\Network as Network;

class FormController extends Controller
{
    /**
     * Get all forms list.
     *
     * @response {
     *  "data": [forms],
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', 'App\Form');

        return FormResource::collection(Form::where('archived', 0)->get());
    }

    /**
     * Get form's informations.
     *
     * @urlParam id int required Form's id
     *
     * @response {
     *  "data": form,
     * }
     */
    public function show($id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('view', $form);

        if ($form->archived) {
            return response([
                "message" => "That form is archived"
            ], 403);
        }

        return new FormResource($form);
    }

    /**
     * Create a new form.
     *
     * @bodyParam name string required Form's name
     * @bodyParam campaign_id int required Form's campaign id
     * @bodyParam network_id int required Form's network id
     * @bodyParam product_id int required Form's product id
     * @bodyParam quantity string required Product's quantity
     * @bodyParam price float required Product's price
     * @bodyParam commission float required Product's conversion commission
     * @bodyParam sub_commission float required Product's conversion sub commission
     * @bodyParam traffic_source_id int required Form's traffic source id
     * @bodyParam network_params json required Form params depending on the network
     *
     * @response 201 {
     *  "message": "Form created",
     *  "data": [form],
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Form');

        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
            'campaign_id' => 'required|integer|exists:campaigns,id',
            'network_id' => 'required|integer|exists:networks,id',
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'string|min:1',
            'price' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
            'sub_commission' => 'required|numeric|min:0',
            'traffic_source_id' => 'required|integer|exists:traffic_sources,id',
            'network_params' => 'required|json',
        ]);
        $params['user_id'] = Auth::user()->id;
        $params['network_params'] = json_decode($params['network_params'], TRUE);

        // Create form
        $form = Form::create($params);

        // Generate HTML
        $params['form_id'] = $form->id;
        $params['network'] = $form->network->name;
        $output = Form::generateHTML($params);

        if (!$output['error']) {
            $form->info = $output['info'];

            $form->save();
        } else {
            $form->delete();
        }

        return response([
            "message" => "Form created",
            "data" => new FormResource($form),
            "html" => $output['form_html'],
        ], 201);
    }



    /**
     * Update the form's fields.
     *
     * @urlParam id int required Form's id
     *
     * @bodyParam name string Form's name
     * @bodyParam network_id int Form's network id
     * @bodyParam product_id int Form's product id
     * @bodyParam quantity string Product's quantity
     * @bodyParam price float Product's price
     * @bodyParam commission float Product's conversion commission
     * @bodyParam sub_commission float Product's conversion sub commission
     * @bodyParam traffic_source_id int Form's traffic source id
     * @bodyParam network_params json required Form params depending on the network
     *
     * @response {
     *  "data": form,
     * }
     */
    public function update(Request $request, $id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('update', $form);

        if ($form->archived) {
            return response([
                "message" => "That form is archived"
            ], 403);
        }

        // Params validation
        $params = $request->validate([
            'name' => 'string|min:3',
            'network_id' => 'integer|exists:networks,id',
            'product_id' => 'integer|exists:products,id',
            'quantity' => 'string|min:1',
            'price' => 'numeric',
            'commission' => 'numeric',
            'sub_commission' => 'numeric',
            'traffic_source_id' => 'integer|exists:traffic_sources,id',
            'network_params' => 'required|json',
        ]);
        $params['network_params'] = json_decode($params['network_params'], TRUE);

        $form->update($params);

        return new FormResource($form);
    }

    /**
     * Delete the specified form.
     *
     * @urlParam id int required Form's id
     *
     * @response {
     *  "message": "Form has been archived",
     * }
     */
    public function destroy($id)
    {
        $form = Form::findOrFail($id);

        $this->authorize('delete', $form);

        // Archive the form
        $form->archived = 1;
        $form->save();

        return response([
            'message' => 'Form has been archived'
        ]);
    }

    /**
     * Get form stats.
     *
     * @urlParam id int required Form's id
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
        $form = Form::findOrFail($id);
        $this->authorize('view', $form);

        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
        ]);

        $stats = FormStats::where('form_id', $id);
        if (isset($params['filter_date_from'])) {
            $stats = $stats->where('day', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $stats = $stats->where('day', '<=', $params['filter_date_to']);
        }
        $stats = $stats->orderBy('day', 'desc');

        return FormStatsResource::collection($stats->get());
    }

    /**
     * Edit form stats
     *
     * @urlParam id int required Form's id
     *
     * @bodyParam form_stats_id int required Form stats id
     * @bodyParam total_cost float required Form stats total_cost new value
     *
     * @response {
     *  "data": [stats]
     * }
     */
    public function updateStats($id, Request $request)
    {
        $form = Form::findOrFail($id);
        $this->authorize('update', $form);

        // Param validation
        $params = $request->validate([
            'form_stats_id' => 'required|integer|exists:form_stats,id',
            'total_cost' => 'required|numeric|min:0',
        ]);

        $stats = FormStats::findOrFail($params['form_stats_id']);
        $stats->total_cost = $params['total_cost'];
        $stats->save();

        return new FormStatsResource($stats);
    }

    /**
     * Get form code
     *
     * @urlParam id int required Form's id
     *
     * @response {
     *  "data": code
     * }
     */
    public function code($id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('view', $form);

        $params = [
            'name' => $form->name,
            'campaign_id' => $form->campaign_id,
            'network_id' => $form->network_id,
            'product_id' => $form->product_id,
            'quantity' => $form->quantity,
            'price' => $form->price,
            'commission' => $form->commission,
            'sub_commission' => $form->sub_commission,
            'traffic_source_id' => $form->traffic_source_id,
            'network_params' => json_decode(json_encode($form->network_params), TRUE),
            'user_id' => $form->user_id,
            'form_id' => $form->id,
            'network' => $form->network->name,
        ];

        $output = $form->generateHTML($params);
        if ($output['error']) {
            return response([
                "message" => "Unexpected error in generating form code"
            ], 500);
        }

        return response([
            'data' => $output['form_html']
        ]);
    }

    /**
     * Form is visited.
     *
     * @urlParam id int required Form's id
     *
     * @response {
     *  "data": [forms],
     * }
     */
    public function visited($id)
    {
        $form = Form::findOrFail($id);
        $form->visits += 1;
        $form->save();

        $formStats = FormStats::getObject($id, date('Y-m-d'));
        $formStats->visits += 1;
        $formStats->save();
    }
}
