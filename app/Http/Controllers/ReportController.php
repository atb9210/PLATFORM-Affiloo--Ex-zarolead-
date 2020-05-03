<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Order;
use App\Http\Controllers\OrderController;
use App\Http\Resources\Order as OrderResource;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
	public function calculateUsersReport(Request $request, $permission = "")
	{
		if(!Auth::check()) {
			return redirect()->route('login');
		}
		if(!Auth::user()->hasPermission('admin')) {
			return redirect()->route('dashboard');
		}

		$o = new OrderController();
		$vars = $o->filterNotPaginated($request, true);


		// Date filters
        $filterDate = false;
        $fromDate = date('Y-m-d');
        $from = $fromDate . ' 00:00:00';
        $toDate = date('Y-m-d');
        $to = $toDate . ' 23:59:59';
        if ($request->input('filter_date_from', '') != '') {
            $filterDate = true;
            $fromDate = $request->input('filter_date_from');
            $from = $fromDate . ' 00:00:00';
            $toDate = date('Y-m-d');
            $to = $toDate . ' 23:59:59';
        }
        if ($request->input('filter_date_to', '') != '') {
            $toDate = $request->input('filter_date_to');
            $to = $toDate . ' 23:59:59';
        }

        // Email/ID filters
        $filter_email_id = $request->input('filter_email_id', '');

		$usersCache = [];
		$report = [];
		$vars['orders'] = $vars['orders']->whereBetween('created_at', [$from, $to]);
		foreach ($vars['orders']->get() as $order) {
			if (!array_key_exists($order->user_id, $usersCache)) {
				$user = User::findOrFail($order->user_id);
				$usersCache[$order->user_id] = true;
				if ($permission != '') {
					$usersCache[$order->user_id] = $usersCache[$order->user_id] && $user->hasPermission($permission);
				}
				if ($filter_email_id != '') {
					$usersCache[$order->user_id] = $usersCache[$order->user_id] &&
						(strpos($user->email, $filter_email_id) !== false || $user->id == $filter_email_id);
				}
			}

			if ($usersCache[$order->user_id]) {
				if (!array_key_exists($order->user_id, $report)) {
					$report[$order->user_id] = array(
						'id' => $order->user_id,
						'email' => User::find($order->user_id)->email,
						'leads' => 0,
						'commissions' => 0.00,
						'sub_commissions' => 0.00,
					);
				}
				$report[$order->user_id]['leads'] += 1;
				$report[$order->user_id]['commissions'] += floatval($order->commission);

				$order_info = json_decode($order->info, TRUE);
				if (isset($order_info['sub_commission'])) {
					$report[$order->user_id]['sub_commissions'] += floatval($order_info['sub_commission']);
				}
			}
		}

		$vars = array(
			'users_report' => $report,
			'users_count' => array(
				'Tutti' => array(
					'count' => count($report),
				),
			),

			'filter_email_id' => $filter_email_id,
			'filter_date_from' => $fromDate,
			'filter_date_to' => $toDate,
			
			'page_size' => 20,
		);
		return $vars;
	}

	public function calculateProductsReport(Request $request)
	{
		if(!Auth::check()) {
			return redirect()->route('login');
		}
		if(!Auth::user()->hasPermission('admin')) {
			return redirect()->route('dashboard');
		}

		$o = new OrderController();
		$vars = $o->filterNotPaginated($request, true);

		$report = [];
		foreach ($vars['orders']->get() as $order) {
			if (!array_key_exists($order->product, $report)) {
				$report[$order->product] = array(
					'product' => $order->product,
					'leads' => 0,
					'commissions' => 0.00,
				);
			}
			$report[$order->product]['leads'] += 1;
			$report[$order->product]['commissions'] += floatval($order->commission);
		}

		$vars['productsReport'] = $report;
		return $vars;
	}

	public function calculateNetworksReport(Request $request)
	{
		if(!Auth::check()) {
			return redirect()->route('login');
		}
		if(!Auth::user()->hasPermission('admin')) {
			return redirect()->route('dashboard');
		}

		$o = new OrderController();
		$vars = $o->filterNotPaginated($request, true);

		$report = [];
		foreach ($vars['orders']->get() as $order) {
			if (!array_key_exists($order->network, $report)) {
				$report[$order->network] = array(
					'network' => $order->network,
					'leads' => 0,
					'commissions' => 0.00,
				);
			}
			$report[$order->network]['leads'] += 1;
			$report[$order->network]['commissions'] += floatval($order->commission);
		}

		$vars['networksReport'] = $report;
		return $vars;
	}

	public function usersReport(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }
        if(!Auth::user()->hasPermission('admin')) {
			return redirect()->route('dashboard');
		}
        return view('report.users')->with($this->calculateUsersReport($request));
	}

	public function productsReport(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }
        if(!Auth::user()->hasPermission('admin')) {
			return redirect()->route('dashboard');
		}
        return view('report.products')->with($this->calculateProductsReport($request));
	}

	public function networksReport(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }
        if(!Auth::user()->hasPermission('admin')) {
			return redirect()->route('dashboard');
		}
        return view('report.networks')->with($this->calculateNetworksReport($request));
	}

	public function mediabuyersReport(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }
        if(!Auth::user()->hasPermission('admin')) {
			return redirect()->route('dashboard');
		}
        return view('report.mediabuyer')->with($this->calculateUsersReport($request, 'mediabuyer'));
	}

	public function affiliatesReport(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }
        if(!Auth::user()->hasPermission('admin')) {
			return redirect()->route('dashboard');
		}
        return view('report.affiliates')->with($this->calculateUsersReport($request, 'affiliate'));
	}

	public function advertisersReport(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }
        if(!Auth::user()->hasPermission('admin')) {
			return redirect()->route('dashboard');
		}
        return view('report.advertisers')->with($this->calculateNetworksReport($request));
	}
}
