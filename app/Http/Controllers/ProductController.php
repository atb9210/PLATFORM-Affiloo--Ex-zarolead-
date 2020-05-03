<?php

namespace App\Http\Controllers;

use App\Product as Obj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
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

        return view('assets/products')->with([
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

        return view('assets/product');
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
		$tag_id = $request->input('tag_id', '');
		$advertiser_id = $request->input('advertiser_id', '');
		$country = $request->input('country', '');

		if ($name == '') {
			return view('assets/product')->with([
				'error' => 'Name must not be empty'
			]);
		}

		if (!is_numeric($tag_id) || $tag_id <= 0) {
			return view('assets/product')->with([
				'error' => 'Tag ID must not be less or equal than zero'
			]);
		}

		if (!is_numeric($advertiser_id) || $advertiser_id <= 0) {
			return view('assets/product')->with([
				'error' => 'Advertiser ID must not be less or equal than zero'
			]);
		}

		$obj = Obj::create([
			'name' => $name,
			'tag_id' => $tag_id,
			'advertiser_id' => $advertiser_id,
			'country' => $country,
		]);

		return view('assets/product')->with([
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

        return view('assets/product')->with([
            'id' => $obj->id,
            'object' => $obj,
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
		$tag_id = $request->input('tag_id', '');
		$advertiser_id = $request->input('advertiser_id', '');
		$country = $request->input('country', '');

        if ($name == '') {
			return view('assets/product')->with([
				'error' => 'Name must not be empty'
			]);
		}

        if (!is_numeric($tag_id) || $tag_id <= 0) {
			return view('assets/product')->with([
				'error' => 'Tag ID must not be less or equal than zero'
			]);
		}

		if (!is_numeric($advertiser_id) || $advertiser_id <= 0) {
			return view('assets/product')->with([
				'error' => 'Advertiser ID must not be less or equal than zero'
			]);
		}

        $obj = Obj::findOrFail($id);

        $obj->update([
            'name' => $name,
			'tag_id' => $tag_id,
			'advertiser_id' => $advertiser_id,
			'country' => $country,
        ]);
        $obj->save();

        return view('assets/product')->with([
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
