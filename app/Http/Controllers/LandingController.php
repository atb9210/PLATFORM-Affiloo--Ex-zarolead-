<?php

namespace App\Http\Controllers;

use App\Landing as Obj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
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

        return view('assets/landings')->with([
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

        return view('assets/landing');
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
		$product_id = $request->input('product_id', '');
		$tag_id = $request->input('tag_id', '');
		$url = $request->input('url', '');
		$country = $request->input('country', '');

		if ($name == '') {
			return view('assets/landing')->with([
				'error' => 'Name must not be empty'
			]);
		}

		if (!is_numeric($tag_id) || $tag_id <= 0) {
			return view('assets/landing')->with([
				'error' => 'Tag ID must not be less or equal than zero'
			]);
		}

		if (!is_numeric($product_id) || $product_id <= 0) {
			return view('assets/landing')->with([
				'error' => 'Product ID must not be less or equal than zero'
			]);
		}

		if ($url == '') {
			return view('assets/landing')->with([
				'error' => 'URL must not be empty'
			]);
		}
		if (strpos($url, 'http://') !== 0) {
			$url = 'http://' . $url;
		}

		$obj = Obj::create([
			'name' => $name,
			'product_id' => $product_id,
			'tag_id' => $tag_id,
			'url' => $url,
			'country' => $country,
		]);

		return view('assets/landing')->with([
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

        return view('assets/landing')->with([
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
		$product_id = $request->input('product_id', '');
		$tag_id = $request->input('tag_id', '');
		$url = $request->input('url', '');
		$country = $request->input('country', '');

        if ($name == '') {
			return view('assets/landing')->with([
				'error' => 'Name must not be empty'
			]);
		}

        if (!is_numeric($tag_id) || $tag_id <= 0) {
			return view('assets/landing')->with([
				'error' => 'Tag ID must not be less or equal than zero'
			]);
		}

		if (!is_numeric($product_id) || $product_id <= 0) {
			return view('assets/landing')->with([
				'error' => 'Product ID must not be less or equal than zero'
			]);
		}

		if ($url == '') {
			return view('assets/landing')->with([
				'error' => 'URL must not be empty'
			]);
		}
		if (strpos($url, 'http://') !== 0) {
			$url = 'http://' . $url;
		}

        $obj = Obj::findOrFail($id);

        $obj->update([
            'name' => $name,
			'product_id' => $product_id,
			'tag_id' => $tag_id,
			'url' => $url,
			'country' => $country,
        ]);
        $obj->save();

        return view('assets/landing')->with([
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
