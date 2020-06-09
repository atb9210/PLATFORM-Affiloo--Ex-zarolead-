<?php

namespace App\Http\Controllers;

use App\Form;
use App\FormStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Form as FormResource;
use App\Http\Controllers\FormBuilderController;

class FormController extends Controller {
	private $permissions = [
        'view' => ['admin', 'mediabuyer', 'affiliate'],
        'edit' => ['admin', 'mediabuyer'],
	];

	private function permissionGuard($action = 'view') {
		$redirect = true;
        foreach ($this->permissions[$action] as $permission) {
        	if (Auth::user()->hasPermission($permission)) {
        		$redirect = false;
        		break;
        	}
        }
        return (!$redirect);
	}

    public function getForm($id) {
        $form = Form::findOrFail($id);

        $form_plugins = [];
        if (isset($form->plugins)) {
            $form_plugins = $form->plugins;
        }

        $plugins = [];
        foreach($form_plugins as $key => $value) {
            if ($value['enabled']) {
                $plugins[$key] = $value;
            }
        }

        return view('form_get')->with([
            'form_id' => $form->id,
            'plugins' => $plugins,
        ]);
    }

    public function calculateFormsReport(Request $request, $admin = false) {
		if(!Auth::check()) {
			return redirect()->route('login');
		}

        $admin = $admin && Auth::user()->hasPermission('admin');

		// Retrieve forms
		$forms = [];
		if ($admin) {
			$forms = Form::where('id', '>', 0);
		} else {
			$forms = Form::where('user_id', Auth::user()->id);
		}

		// @TODO:
		$filter_email_id = '';

        $fromDate = $request->input('filter_date_from');
        $toDate = $request->input('filter_date_to');

        $form_stats = array();
        foreach ($forms->get() as $form) {
            $form_stats[$form->id] = $form->getTotalStats($fromDate, $toDate, $admin);
        }

        $page_size = 500;
        if (is_numeric($request->input('page_size', 0)) && $request->input('page_size', 0) > 0) {
            $page_size = $request->input('page_size');
        }

        $formsCollection = FormResource::collection($forms->paginate($page_size));

		return array(
			'forms' => $formsCollection,
            'form_stats' => $form_stats,
			'forms_count' => array(
				'Tutti' => array(
					'count' => count($forms->get()),
				),
			),

			'filter_email_id' => $filter_email_id,
			'filter_date_from' => $fromDate,
			'filter_date_to' => $toDate,
			
			'page_size' => $page_size,

            'admin' => $admin,
		);
	}

	public function index(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('view')) {
        	return redirect()->route('dashboard');
        }

        if (Auth::user()->hasPermission('admin')) {
            return redirect()->route('admin/forms');
        }

        return view('forms')->with($this->calculateFormsReport($request));
	}

	public function adminIndex(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }
        if(!Auth::user()->hasPermission('admin')) {
			return redirect()->route('forms');
		}

		if (!$this->permissionGuard()) {
        	return redirect()->route('dashboard');
        }

        return view('forms')->with($this->calculateFormsReport($request, true));
	}

	public function delete($id) {
        // Delete the order
        $form = Form::findOrFail($id);

        if ($form->user_id != Auth::user()->id && !Auth::user()->hasPermission('admin')) {
            return array(
                "result" => false
            );
        }

        if ($form->delete())
        {
            return new FormResource($form);
        }
    }

    public function getCode($id) {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    	$form = Form::findOrFail($id);

        if ($form->user_id != Auth::user()->id && !Auth::user()->hasPermission('admin')) {
            return redirect()->route('forms');
        }

        $formBuilder = new FormBuilderController();
        $output = $formBuilder->generateHTML($form->info['build_data']);

        return view('form-builder')->with([
        	'form_generated' => !$output['error'],

        	'form_html' => $output['form_html']
        ]);
    }

    public function dailyReports($id, Request $request) {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $form = Form::findOrFail($id);
        if (!Auth::user()->hasPermission('admin') && $form->user_id != Auth::user()->id) {
            return redirect()->route('forms');
        }

        $fromDate = $request->input('filter_date_from', '');
        $toDate = $request->input('filter_date_to', '');

        $page_size = 500;

        $reports = FormStats::where('form_id', $id);

        if ($fromDate == '' && $toDate != '') {
            $reports = $reports->where('day', '<=', $toDate);
        } elseif ($fromDate != '' && $toDate == '') {
            $reports = $reports->where('day', '>=', $fromDate);
        } elseif ($fromDate != '' && $toDate != '') {
            $reports = $reports->whereBetween('day', [$fromDate, $toDate]);
        }

        $reports = $reports->orderBy('day', 'desc');

        return view('form')->with([
            'form' => $form,
            
            'reports' => $reports->paginate($page_size),

            //'form_stats' => $form_stats,
            'forms_count' => array(
                'Tutti' => array(
                    'count' => count($reports->get()),
                ),
            ),

            'filter_date_from' => $fromDate,
            'filter_date_to' => $toDate,
            
            'page_size' => $page_size,
        ]);
    }

    public function visited(Request $request) {
        $formStats = FormStats::getObject($request->input('form_id'), date('Y-m-d'));
        $formStats->visits += 1;
        $formStats->save();

        $form = Form::findOrFail($request->input('form_id'));
        $form->visits += 1;
        $form->save();

        return array(
            "result" => true,
        );
    }

    public function setDailyCost(Request $request) {
        $toReturn = array(
            'result' => false,
            'message' => 'Unexpected error!',
        );

        $form_stats_id = $request->input('form_stats_id', 0);

        if (!is_numeric($form_stats_id) || $form_stats_id <= 0) {
            $toReturn['message'] = 'Form does not exist!';
            return $toReturn;
        }

        // @TODO: How to check check in API?
        /*
        if (!Auth::check()) {
            $toReturn['message'] = 'User not logged in!';
            return $toReturn;
        }
        */
        

        $formStats = FormStats::findOrFail($form_stats_id);
        $form = Form::findOrFail($formStats->form_id);

        // @TODO: How to check check in API?
        /*
        if (!Auth::user()->hasPermission('admin') && $form->user_id != Auth::user()->id) {
            $toReturn['message'] = 'User does not have permission!';
            return $toReturn;
        }
        */

        $total_cost = floatval($request->input('total_cost', 0));
        $form->total_cost += $total_cost - $formStats->total_cost;
        $formStats->total_cost = $total_cost;

        $formStats->save();
        $form->save();

        $toReturn['result'] = true;
        $toReturn['message'] = 'Total cost modified.';

        return $toReturn;
    }

    public function edit($id) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('edit')) {
            return redirect()->route('dashboard');
        }

        $form = Form::findOrFail($id);

        return view('form-edit')->with([
            'network' => $form->network,
            'form' => $form,
        ]);
    }

    public function update($id, Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('edit')) {
            return redirect()->route('dashboard');
        }

        $form = Form::findOrFail($id);

        $data = $request->all();

        $generated = true;

        $price = floatval($request->input('price'));
        $commission = floatval($request->input('commission'));
        $sub_commission = floatval($request->input('sub_commission'));

        if (isset($data['zeus_track']) && $data['zeus_track']) {
            $data['zeus_track'] = true;
        } else {
            $data['zeus_track'] = false;
        }

        $form->fill([
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'network' => $data['network'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'price' => $price,
            'commission' => $commission,
            'sub_commission' => $sub_commission,
            'tag_id' => $data['tag_id'],
            'traffic_source_id' => $data['traffic_source_id'],
        ]);

        $data['form_id'] = $form->id;
        $formBuilderController = new FormBuilderController();
        $output = $formBuilderController->generateHTML($data);

        if (!$output['error']) {
            $form->user_id = $output['info']['build_data']['user_id'];
            $form->network = $output['network']['name'];
            $form->info = $output['info'];

            $form->save();
        } else {
            return view('form-edit')->with([
                'form_edited' => false,
                'error' => true,
                'message' => 'Unexpected error!',
            ]);
        }

        return view('form-edit')->with([
            'error' => false,
            'form_edited' => !$output['error'],
        ]);
    }
}
