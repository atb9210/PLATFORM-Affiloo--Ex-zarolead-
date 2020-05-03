<?php

namespace App\Http\Controllers;

use App\TrafficSource as Obj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrafficSourceController extends Controller
{
    private $permissions = [
		'view' => ['admin'],
        'create' => ['admin'],
        'edit' => ['admin'],
        'delete' => ['admin'],
    ];

    private function permissionGuard($action = 'view')
    {
        $result = false;
        foreach ($this->permissions[$action] as $permission) {
            if (Auth::user()->hasPermission($permission)) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    public function index()
    {
    	if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('view')) {
            return redirect()->route('dashboard');
        }

        return view('assets/traffic-sources')->with([
        	'objects' => Obj::all(),
        ]);
    }

	public function createIndex()
    {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('create')) {
            return redirect()->route('dashboard');
        }

        return view('assets/traffic-source');
    }


	public function store(Request $request)
	{
		if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('create')) {
            return redirect()->route('dashboard');
        }

		$name = $request->input('name', '');

		if ($name == '') {
			return view('assets/traffic-source')->with([
				'error' => 'Name must not be empty'
			]);
		}

		$max_custom_params = 10;
		$custom_params = array();
		for ($i = 0; $i < $max_custom_params; $i += 1) {
			$cp_query_param = $request->input('query_param' . $i, '');
			$cp_token = $request->input('token' . $i, '');
			$cp_name = $request->input('name' . $i, '');
			if ($cp_query_param != '' &&
				$cp_token != '' &&
				$cp_name != '') {
				array_push($custom_params, array(
					'query_param' => $cp_query_param,
					'token' => $cp_token,
					'name' => $cp_name,
				));
			}
		}

		$obj = Obj::create([
			'name' => $name,
			'custom_params' => $custom_params,
		]);

		return view('assets/traffic-source')->with([
			'success' => true,
			'object' => $obj,
		]);
	}

    public function edit($id)
    {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('edit')) {
            return redirect()->route('dashboard');
        }

        $obj = Obj::findOrFail($id);

        return view('assets/traffic-source')->with([
            'id' => $obj->id,
            'object' => $obj,
            'name' => $obj->name,
            'custom_params' => $obj->custom_params,
            'action' => 'update',
        ]);
    }

    public function update($id, Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('edit')) {
            return redirect()->route('dashboard');
        }

        $name = $request->input('name', '');

		if ($name == '') {
			return view('assets/traffic-source')->with([
				'error' => 'Name must not be empty'
			]);
		}

        $max_custom_params = 10;
		$custom_params = array();
		for ($i = 0; $i < $max_custom_params; $i += 1) {
			$cp_query_param = $request->input('query_param' . $i, '');
			$cp_token = $request->input('token' . $i, '');
			$cp_name = $request->input('name' . $i, '');
			if ($cp_query_param != '' &&
				$cp_token != '' &&
				$cp_name != '') {
				array_push($custom_params, array(
					'query_param' => $cp_query_param,
					'token' => $cp_token,
					'name' => $cp_name,
				));
			}
		}

        $obj = Obj::findOrFail($id);

        $obj->update([
            'name' => $name,
            'custom_params' => $custom_params,
        ]);
        $obj->save();

        return view('assets/traffic-source')->with([
            'success' => true,
            'object' => $obj,
        ]);
    }

	public function delete($id)
    {
        // Delete
        $obj = Obj::findOrFail($id);

        if (!$this->permissionGuard('delete')) {
            return array(
                "result" => false
            );
        }

        // Delete record
        if ($obj->delete())
        {
            return $obj;
        }
    }
}
