<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Form as Form;
use App\Http\Resources\api\v1\FormResource;
use App\FormStats as FormStats;
use App\Http\Resources\api\v1\FormStatsResource;
use App\FormNote as FormNote;
use App\Network as Network;
use App\Landing as Landing;
use App\LandingStats as LandingStats;
use App\UserReport as UserReport;

class FormController extends Controller
{
    /**
     * Get all forms list (also the archived ones).
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     *
     * @response {
     *  "data": [forms],
     * }
     */
    public function index(Request $request)
    {
        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
        ]);

        $forms = Form::query();
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
     *  "data": form,
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

        $form_html  = '<iframe scrolling="no" id="buy-frame" width="100%" height="500px" src="' . route('forms.get', ['id' => $form->id]) . '"></iframe>' . PHP_EOL;
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

        $output = [
            'form_html' => $form_html,
        ];

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

        // Update total cost
        $prev_total_cost = $stats->total_cost;
        $stats->total_cost = $params['total_cost'];
        $form->total_cost += $stats->total_cost - $prev_total_cost;

        $form->save();
        $stats->save();

        // Update user's report stats
        $report = UserReport::getObject($form->user->id, $stats->day);
        $report->cost += $stats->total_cost - $prev_total_cost;
        $report->profit = $report->commission - ($report->cost + $report->extra_cost);
        $report->save();

        return new FormStatsResource($stats);
    }

    /**
     * Get landings stats (of this form)
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
    public function landingsStats($id, Request $request)
    {
        $form = Form::findOrFail($id);
        $this->authorize('view', $form);

        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
        ]);

        $data = [];
        foreach($form->landings as $landing) {
            $stats = DB::table('landing_stats')->where('landing_id', $landing->id);
            if (isset($params['filter_date_from'])) {
                $stats = $stats->where('day', '>=', $params['filter_date_from']);
            }
            if (isset($params['filter_date_to'])) {
                $stats = $stats->where('day', '<=', $params['filter_date_to']);
            }

            $visits = 0;
            $leads = 0;
            $total_cost = 0;
            $total_commission = 0;
            if($stats->get()->count() > 0) {
                $visits = $stats->sum('visits');
                $leads = $stats->sum('leads');
                $total_cost = $stats->sum('total_cost');
                $total_commission = $stats->sum('total_commission');
            }
            array_push($data, [
                'id' => $landing->id,
                'name' => $landing->name,
                'visits' => $visits,
                'leads' => $leads,
                'total_cost' => $total_cost,
                'total_commission' => $total_commission,
            ]);
        }

        return response([
            'data' => $data
        ], 200);
    }

    /**
     * Get form code
     *
     * @urlParam id int required Form's id
     *
     * @bodyParam landing_id int Landing id
     *
     * @response {
     *  "data": code
     * }
     */
    public function code($id, Request $request)
    {
        $form = Form::findOrFail($id);
        $this->authorize('view', $form);

        // Param validation
        $params = $request->validate([
            'landing_id' => 'integer|exists:landings,id',
        ]);

        $form_url = route('forms.get', ['id' => $form->id]);
        if (isset($params['landing_id'])) {
            $landing = Landing::findOrFail($params['landing_id']);
            if ($landing->form_id === $form->id) {
                $form_url .= '?landing=' . $landing->id;
            }
        }

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

        return response([
            'data' => $form_html,
        ]);
    }

    /**
     * Form is visited.
     *
     * @urlParam id int required Form's id
     *
     * @bodyParam landing_id int Landing id
     *
     * @response {
     *  "data": "",
     * }
     */
    public function visited($id, Request $request)
    {
        // Params validation
        $params = $request->validate([
            'landing_id' => 'integer|exists:landings,id',
        ]);

        $form = Form::findOrFail($id);
        $form->visits += 1;
        $form->save();

        $today = date('Y-m-d');

        $formStats = FormStats::getObject($id, $today);
        $formStats->visits += 1;
        $formStats->save();

        if (isset($params['landing_id'])) {
            $landing = Landing::find($params['landing_id']);
            if($landing != null && $landing->form_id == $form->id) {
                $landingStats = LandingStats::getObject($landing->id, $today);
                $landingStats->visits += 1;
                $landingStats->save();
            }
        }
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

    /**
     * Create a note for the specified form
     *
     * @urlParam id int required Form's id
     *
     * @bodyParam body string required Note's body
     *
     * @response 201 {
     *  "message": "Form's note created",
     *  "data": [form],
     * }
     */
    public function createNote($id, Request $request)
    {
        $this->authorize('create', 'App\FormNote');
        $form = Form::findOrFail($id);

        // Param validation
        $params = $request->validate([
            'body' => 'required|string|min:1',
        ]);
        $params['user_id'] = Auth::user()->id;
        $params['form_id'] = $form->id;

        $note = FormNote::create($params);

        return response([
            "message" => "Form's note created",
            "data" => new FormResource($form),
        ], 201);
    }

    /**
     * Edit a note
     *
     * @urlParam id int required Note's id
     *
     * @bodyParam body string Note's body
     *
     * @response {
     *  "data": [form],
     * }
     */
    public function editNote($id, Request $request)
    {
        $note = FormNote::findOrFail($id);
        $this->authorize('update', $note);

        if ($note->form->archived) {
            return response([
                "message" => "That form is archived"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'body' => 'string|min:1',
        ]);

        $note->update($params);

        return new FormResource($note->form);
    }

    /**
     * Delete a note
     *
     * @urlParam id int required Note's id
     *
     * @response {
     *  "message": "Form's note has been deleted",
     * }
     */
    public function deleteNote($id)
    {
        $note = FormNote::findOrFail($id);

        $this->authorize('delete', $note);

        if ($note->form->archived) {
            return response([
                "message" => "That form is archived"
            ], 403);
        }

        $note->delete();

        return response([
            'message' => 'Form\'s note has been deleted'
        ]);
    }

    /**
     * Clone a form.
     *
     * @urlParam form_id integer required Form's name
     *
     * @response 201 {
     *  "message": "Form created",
     *  "data": [form],
     * }
     */
    public function clone(Request $request)
    {
        $this->authorize('create', 'App\Form');

        // Params validation
        $params = $request->validate([
            'form_id' => 'required|integer|exists:forms,id',
        ]);

        $form = Form::findOrFail($params['form_id']);
        $this->authorize('view', $form);

        $newForm = $form->replicate();
        $newForm->push();

        $form_html  = '<iframe scrolling="no" id="buy-frame" width="100%" height="500px" src="' . route('forms.get', ['id' => $newForm->id]) . '"></iframe>' . PHP_EOL;
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

        $output = [
            'form_html' => $form_html,
        ];

        return response([
            "message" => "Form created",
            "data" => new FormResource($newForm),
            "html" => $output['form_html'],
        ], 201);
    }
}
