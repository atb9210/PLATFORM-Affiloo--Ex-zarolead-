<?php

namespace App\Http\Controllers;

use App\Advertiser as Obj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvertiserController extends Controller {
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

        return view('assets/advertisers')->with([
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

        return view('assets/advertiser');
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
			return view('assets/advertiser')->with([
				'error' => 'Name must not be empty'
			]);
		}

		$obj = Obj::create([
			'name' => $name,
		]);

		return view('assets/advertiser')->with([
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

        return view('assets/advertiser')->with([
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

        $obj->update([
            'name' => $request->input('name', $obj->name),
        ]);
        $obj->save();

        return view('assets/advertiser')->with([
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
