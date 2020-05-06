<?php

namespace App\Http\Controllers;

use App\Tag as Obj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller {
    private $permissions = [
		'view' => ['admin'],
        'create' => ['admin'],
        'edit' => ['admin'],
        'delete' => ['admin'],
    ];

    private function permissionGuard($action = 'view') {
        $result = false;
        foreach ($this->permissions[$action] as $permission) {
            if (Auth::user()->hasPermission($permission)) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    public function index() {
    	if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('view')) {
            return redirect()->route('dashboard');
        }

        return view('assets/tags')->with([
        	'objects' => Obj::all(),
        ]);
    }

	public function createIndex() {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('create')) {
            return redirect()->route('dashboard');
        }

        return view('assets/tag');
    }


	public function store(Request $request) {
		if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('create')) {
            return redirect()->route('dashboard');
        }

		$name = $request->input('name', '');

		if ($name == '') {
			return view('assets/tag')->with([
				'error' => 'Name must not be empty'
			]);
		}

		$info = array();

        $default_bg_color = isset($info['bg_color']) ? $info['bg_color'] : 'white';
        $default_fg_color = isset($info['fg_color']) ? $info['fg_color'] : 'black';
        $info['bg_color'] = $request->input('info_bg_color', $default_bg_color);
        $info['fg_color'] = $request->input('info_fg_color', $default_fg_color);

		$obj = Obj::create([
			'name' => $name,
			'info' => $info,
		]);

		return view('assets/tag')->with([
			'success' => true,
			'object' => $obj,
		]);
	}

    public function edit($id) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->permissionGuard('edit')) {
            return redirect()->route('dashboard');
        }

        $obj = Obj::findOrFail($id);

        return view('assets/tag')->with([
            'id' => $obj->id,
            'name' => $obj->name,
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

        $obj = Obj::findOrFail($id);

        $info = $obj->info;

        $default_bg_color = isset($info['bg_color']) ? $info['bg_color'] : 'white';
        $default_fg_color = isset($info['fg_color']) ? $info['fg_color'] : 'black';
        $info['bg_color'] = $request->input('info_bg_color', $default_bg_color);
        $info['fg_color'] = $request->input('info_fg_color', $default_fg_color);

        $obj->update([
            'name' => $request->input('name', $obj->name),
            'info' => $info,
        ]);
        $obj->save();

        return view('assets/tag')->with([
            'success' => true,
            'object' => $obj,
        ]);
    }

	public function delete($id) {
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
