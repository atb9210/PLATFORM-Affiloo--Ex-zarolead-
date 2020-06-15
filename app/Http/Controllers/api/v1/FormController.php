<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        $forms = Form::query();
        $forms = $forms->where('archived', 0);
        if (!Auth::user()->can('viewAny', 'App\Form')) {
            $forms = $forms->where('user_id', Auth::user()->id);
        }

        return FormResource::collection($forms->get());
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
     * @bodyParam quantity json required Product's quantity JSON
     * @bodyParam upsell json required Product's quantity JSON
     * @bodyParam traffic_source_id int required Form's traffic source id
     * @bodyParam network_params json required Form params depending on the network
     * @bodyParam plugins json Form plugins
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
            'quantity' => 'required|json',
            'upsell' => 'required|json',
            'traffic_source_id' => 'required|integer|exists:traffic_sources,id',
            'network_params' => 'required|json',
            'plugins' => 'json',
        ]);
        $params['user_id'] = Auth::user()->id;
        $params['quantity'] = json_decode($params['quantity'], TRUE);
        $params['upsell'] = json_decode($params['upsell'], TRUE);
        $params['network_params'] = json_decode($params['network_params'], TRUE);
        if (isset($params['plugins'])) {
            $params['plugins'] = json_decode($params['plugins'], TRUE);
        }

        // Validate quantity JSON
        $validator = Validator::make($params['quantity'], [
            'options' => 'required|array',
            'default' => 'required',
        ]);
        if ($validator->fails()) {
            return response([
                'message' => 'The given data - for the quantity JSON param - was invalid.',
                'errors' => $validator->messages(),
            ], 422);
        }

        // Validate upsell JSON
        $validator = Validator::make($params['upsell'], [
            'enabled' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response([
                'message' => 'The given data - for the upsell JSON param - was invalid.',
                'errors' => $validator->messages(),
            ], 422);
        }

        // Create form
        $form = Form::create($params);

        // Generate HTML
        $params['form_id'] = $form->id;
        $params['network'] = $form->network->name;
        //$output = Form::generateHTML($params);
        $output = [
            'form_html' => '<iframe width="100%" height="500px" src="' . route('forms.get', ['id' => $form->id]) . '"></iframe>',
        ];

        /*
        if (!$output['error']) {
            $form->info = $output['info'];

            $form->save();
        } else {
            $form->delete();
        }
        */

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
     * @bodyParam quantity json Product's quantity JSON
     * @bodyParam upsell json Product's quantity JSON
     * @bodyParam traffic_source_id int Form's traffic source id
     * @bodyParam network_params json Form params depending on the network
     * @bodyParam plugins json Form plugins
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
            'quantity' => 'json',
            'upsell' => 'json',
            'traffic_source_id' => 'integer|exists:traffic_sources,id',
            'network_params' => 'json',
            'plugins' => 'json',
        ]);
        if (isset($params['quantity'])) {
            $params['quantity'] = json_decode($params['quantity'], TRUE);
        }
        if (isset($params['upsell'])) {
            $params['upsell'] = json_decode($params['upsell'], TRUE);
        }
        if (isset($params['network_params'])) {
            $params['network_params'] = json_decode($params['network_params'], TRUE);
        }
        if (isset($params['plugins'])) {
            $params['plugins'] = json_decode($params['plugins'], TRUE);
        }

        // Validate quantity JSON
        if (isset($params['quantity'])) {
            $validator = Validator::make($params['quantity'], [
                'options' => 'required|array',
                'default' => 'required',
            ]);
            if ($validator->fails()) {
                return response([
                    'message' => 'The given data - for the quantity JSON param - was invalid.',
                    'errors' => $validator->messages(),
                ], 422);
            }
        }

        // Validate upsell JSON
        if (isset($params['upsell'])) {
            $validator = Validator::make($params['upsell'], [
                'enabled' => 'required|boolean',
            ]);
            if ($validator->fails()) {
                return response([
                    'message' => 'The given data - for the upsell JSON param - was invalid.',
                    'errors' => $validator->messages(),
                ], 422);
            }
        }

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

        return response([
            'data' => '<iframe width="100%" height="500px" src="' . route('forms.get', ['id' => $id]) . '"></iframe>'
        ]);

        // @TODO: to delete
        /*
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
        */
    }

    /**
     * Form is visited.
     *
     * @urlParam id int required Form's id
     *
     * @response {
     *  "data": "",
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

    /**
     * Form validate input.
     *
     * @urlParam id int required Form's id
     *
     * @bodyParam input json required Form's input to validate
     *
     * @response {
     *  "data": true,
     * }
     */
    public function validateInput($id, Request $request)
    {
        $form = Form::findOrFail($id);

        // Params validation
        $params = $request->validate([
            'input' => 'required|json',
        ]);
        $params['input'] = json_decode($params['input'], TRUE);

        $rules = $form->network->getApi('form.validation.input');
        $validator = Validator::make($params['input'], $rules);
        if ($validator->fails()) {
            return response([
                'message' => 'The given data was invalid.',
                'errors' => $validator->messages(),
            ], 422);
        }
        return response([
            'data' => true
        ]);
    }

    /**
     * Get form's info for frontend initialization.
     *
     * @urlParam id int required Form's id
     *
     * @response {
     *  "data": [input],
     * }
     */
    public function initialize($id)
    {
        $form = Form::findOrFail($id);

        $input = $form->network->getApi('form.input');
        return response([
            'data' => [
                'input' => $input,
                'quantity' => $form->quantity,
                'upsell' => $form->upsell,
            ],
        ]);
    }
}
