<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
	public function show(Request $request)
	{
		if(!Auth::check()) {
            return redirect()->route('login');
        }

        if(!Auth::user()->hasPermission('admin')) {
        	return redirect()->route('dashboard');
        }

        // Change permission
        if($request->input('change_permission', 0) == 1) {
        	$permissions = array(
        		'admin',
                'mediabuyer',
                'affiliate',
                'advertiser',
                'accountant',
        	);

        	$user_id = $request->input('user_id', 0);
        	if ($user_id > 0) {
        		$user = User::findOrFail($user_id);

        		foreach ($permissions as $permission) {
        			$user->setPermission($permission, $request->input('permission_' . $permission, 0));
        		}
        	}
        }

        $users = array();

        $filterUserNameId = false;
        $userNameId = '';
        if ($request->input('filter_name_id', '') != '') {
            $filterUserNameId = true;
            $userNameId = $request->input('filter_product_name');
        }

        if ($filterUserNameId) {
            $users = User::where('email', 'LIKE', '%' . $userNameId . '%')
                    ->orWhere('id', $userNameId);
        } else {
        	$users = User::query();
        }

        $page_size = 20;
        if (is_numeric($request->input('page_size', 0)) && $request->input('page_size', 0) > 0) {
            $page_size = $request->input('page_size');
        }

        $users = $users->orderBy('created_at', 'desc');
        $rows_count = $users->get()->count();
        $users = UserResource::collection($users->paginate($page_size));

        $params = array(
        	'rows_count' => array(
        		'Tutti' => array(
        			'count' => $rows_count,
        		),
        	),

        	'users' => $users,

        	'filter_email_id' => '',
        	'page_size' => 20,
        );

		return view('users')->with($params);
	}
}
