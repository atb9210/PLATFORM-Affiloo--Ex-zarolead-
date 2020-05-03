<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Http\Controllers\OrderController;
use App\Http\Resources\Order as OrderResource;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if(!Auth::check()) {
			return redirect()->route('login');
		}

		if (Auth::user()->hasPermission('admin')) {
			return redirect()->route('admin/dashboard');
		}

		$o = new OrderController();
	    return view('dashboard')->with($o->filter($request));
    }

    public function filter(Request $request)
    {
    	if(!Auth::check()) {
			return redirect()->route('login');
		}

		$o = new OrderController();
		return view('dashboard')->with($o->filter($request));
    }

    public function adminIndex(Request $request)
    {
    	if(!Auth::check()) {
			return redirect()->route('login');
		}

		$o = new OrderController();
	    return view('dashboard')->with($o->filter($request, true));
    }

    public function adminFilter(Request $request)
    {
    	if(!Auth::check()) {
			return redirect()->route('login');
		}

		$o = new OrderController();
		return view('dashboard')->with($o->filter($request, true));
    }
}
