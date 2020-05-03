<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Order;
use App\OrderStatus;
use App\Form;
use App\FormStats;
use App\Http\Resources\Order as OrderResource;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        // Get all orders
        // TODO: Where's the user?
        $orders = Order::orderBy('created_at', 'desc');
        return $orders;
    }

    public function show($id)
    {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        // Get the order
        $order = Order::findOrFail($id);

        if ($order->user_id != Auth::user()->id && !Auth::user()->hasPermission('admin')) {
            return redirect()->route('orders');
        }

        return view('order')->with('order', $order);
    }

    public function store(Request $request)
    {
        // Create/update order
        $order = $request->isMethod('put') ?
            Order::findOrFail($request->order_id) :
            new Order();

        $user_id = $request->input('user_id') != '' ?
            intval($request->input('user_id')) :
            1;
        $order->user_id = $user_id;

        $order->order_status_id = $request->isMethod('put') ?
            $request->order_status_id :
            1;

        $order->name = $request->input('name');
        $order->phone = $request->input('phone');
        $order->quantity = $request->input('quantity');
		$order->product = $request->input('product');

		$order->commission = $request->input('order_commission');
		$order->network = $request->input('order_network');

		$request->request->remove('product');
		$request->request->remove('order_commission');
		$request->request->remove('order_network');
		$request->request->remove('fb_pixel');
		$request->request->remove('thankyou_page');

        $api_call_url = $request->input('api_call_url');
        $api_call_method = $request->input('api_call_method');
        $network_data = json_decode($request->input('network_data'), TRUE);
		$request->request->remove('api_call_url');
        $request->request->remove('api_call_method');
        $request->request->remove('network_data');

        $sub_commission = $request->input('order_sub_commission');
        $request->request->remove('order_sub_commission');

        $info = $request->all();
        $info['sub_commission'] = $sub_commission;
        $order->info = json_encode($info);

        $server_output = [];
        if ($api_call_url != '') {
            $ch = curl_init();

            $requestMethod = "post";
            if ($api_call_method != "") {
                $requestMethod = strtolower($api_call_method);
            }

            if ($order->network == "affiliationpark") {
                $network_data["user"] = "asrafatb9210@gmail.com";
                $network_data["password"] = "1vrdNLMOjwQJIbB8Eqt0AcUgyDmp2hsC";
            }

            $getUrl = "";
            if ($requestMethod == "post") {
                curl_setopt($ch, CURLOPT_URL, $api_call_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($network_data));
            } else {
                $getUrl = $api_call_url . "?" . http_build_query($network_data);
                curl_setopt($ch, CURLOPT_URL, $getUrl);
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $server_output = curl_exec($ch);
            curl_close ($ch);

            $server_output = json_decode($server_output, true);
        }

        // POSTBACK
        if ($order->network == "amsped" || $order->network == "affiliationpark") {
            $postback_ch = curl_init();
            $postback_data = array(
                'cid' => $request->input('sub_id'),
                'payout' => $order->commission,
            );

            $getUrl = "http://ztrk37.com/postback?" . http_build_query($postback_data);

            curl_setopt($postback_ch, CURLOPT_URL, $getUrl);
            curl_setopt($postback_ch, CURLOPT_RETURNTRANSFER, 1);

            $postback_output = curl_exec($postback_ch);

            curl_close($postback_ch);
        }

        if ($order->save())
        {
            $toReturn = array(
                "result" => true,
                "order" => new OrderResource($order),
                "server_output" => json_encode($server_output),
                "server_input" => json_encode($network_data),
            );

            $api_success_response = [];
            if ($request->input('api_success_response') != '') {
                $api_success_response = json_decode($request->input('api_success_response'));
            }
            $error = false;
            foreach ($api_success_response as $key => $value) {
                if (isset($server_output[$key]) && $server_output[$key] == $value) {
                    continue;
                } else {
                    $error = true;
                    break;
                }
            }
            if (!$error) {
                $this->orderSentToNetwork($order->id);
            } else {
                // TODO: Generalize the algorithm (this only works for worldfilia)
                if (isset($server_output['check_url'])) {
                    $toReturn['redirect'] = $server_output['check_url'];

                    // The order is double type
                    $info['double_type'] = true;

                    $order->info = json_encode($info);
                    $order->save();
                }
            }

            // Increase form lead and commission
            $form_id = $request->input('form_id');
            if ($form_id != '' && $form_id > 0) {
                // Daily
                $formStats = FormStats::getObject($form_id, date('Y-m-d'));
                $formStats->leads += 1;
                $formStats->total_commission += $order->commission;
                $formStatsInfo = $formStats->info;
                if (!isset($formStatsInfo['total_sub_commission'])) {
                    $formStatsInfo['total_sub_commission'] = 0.0;
                }
                $formStatsInfo['total_sub_commission'] += $sub_commission;
                $formStats->info = $formStatsInfo;
                $formStats->save();

                // Total
                $form = Form::find($form_id);
                $form->leads += 1;
                $form->total_commission += $order->commission;
                $formInfo = $form->info;
                if (!isset($formInfo['total_sub_commission'])) {
                    $formInfo['total_sub_commission'] = 0.0;
                }
                $formInfo['total_sub_commission'] += $sub_commission;
                $form->info = $formInfo;
                $form->save();
            }

            return $toReturn;
        }
        return array(
            "result" => false,
            "order" => false,
            "redirect" => $server_output['check_url'],
            "server_output" => json_encode($server_output),
        );
    }

    public function delete($id)
    {
        // Delete the order
        $order = Order::findOrFail($id);

        if ($order->user_id != Auth::user()->id && !Auth::user()->hasPermission('admin')) {
            return array(
                "result" => false
            );
        }

        if ($order->delete())
        {
            return new OrderResource($order);
        }
    }

    public function storeAndReturnView(Request $request)
    {
        $o = $this->store($request);
        return view('order')->with('order', $o);
    }
	
	public function orderSentToNetwork($id)
	{
		$order = Order::findOrFail($id);
		$info = json_decode($order->info);
		$info->network_sent = 1;
		$order->info = json_encode($info);
		$order->save();
	}

    public function ordersCount($orders) {
        $ordersCount = array(
            'Tutti' => array(
                'count' => 0,
                'total' => 0.00,
                'sub_commission' => 0.00,
            ),
            'Attesa' => array(
                'count' => 0,
                'total' => 0.00,
                'sub_commission' => 0.00,
            ),
            'Conferma' => array(
                'count' => 0,
                'total' => 0.00,
                'sub_commission' => 0.00,
            ),
            'Non risponde' => array(
                'count' => 0,
                'total' => 0.00,
                'sub_commission' => 0.00,
            ),
            'Rifiuto' => array(
                'count' => 0,
                'total' => 0.00,
                'sub_commission' => 0.00,
            ),
            'network_sent' => array(
                'count' => 0,
                'total' => 0.00,
                'sub_commission' => 0.00,
            ),
        );
        
        foreach ($orders as $order) {
            $order_info = json_decode($order->info, TRUE);
            $sub_commission = 0.0;

            // Status
            if (!isset($ordersCount[$order->status->description])) {
                $ordersCount[$order->status->description]['count'] = 1;
                $ordersCount[$order->status->description]['total'] = 0.00;
                $ordersCount[$order->status->description]['sub_commission'] = 0.00;
            } else {
                $ordersCount[$order->status->description]['count'] += 1;
                $ordersCount[$order->status->description]['total'] += floatval($order->commission);

                if (isset($order_info['sub_commission'])) {
                    $sub_commission = $order_info['sub_commission'];
                }
                $ordersCount[$order->status->description]['sub_commission'] += floatval($sub_commission);
            }
            
            $ordersCount['Tutti']['count'] += 1;
            $ordersCount['Tutti']['total'] += floatval($order->commission);
            $ordersCount['Tutti']['sub_commission'] += floatval($sub_commission);

            if (isset($order_info['network_sent']) && $order_info['network_sent'] == 1) {
                $ordersCount['network_sent']['count'] += 1;
                $ordersCount['network_sent']['total'] += floatval($order->commission);
                $ordersCount['network_sent']['sub_commission'] += floatval($sub_commission);
            }
        }
        
        return $ordersCount;
    }

    public function filter(Request $request, $admin = false) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        $orders = Order::query();

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

        // Network sent and double type filter
        $filterNetworkSent = $request->input('filter_network_sent', 'all');
        $filterDoubleType = $request->input('filter_double_type', 'all');

        // Product name filters
        $filterProductName = false;
        $productName = '';
        if ($request->input('filter_product_name', '') != '') {
            $filterProductName = true;
            $productName = $request->input('filter_product_name');
        }

        $admin = $admin && Auth::user()->hasPermission('admin');

        $orders = Order::whereBetween('created_at', [$from, $to]);

        if (!$admin) {
            $orders = $orders->where('user_id', Auth::user()->id);
        }

        if ($filterNetworkSent !== 'all') {
            if ($filterNetworkSent == 1) {
                $orders = $orders->where('info->network_sent', $filterNetworkSent);
            } elseif ($filterNetworkSent == 0) {
                $orders = $orders->where(function ($q) {
                    $q->whereNull('info->network_sent')->orWhere('info->network_sent', 0);
                });
            }
        }

        if ($filterDoubleType !== 'all') {
            if ($filterDoubleType) {
                $orders = $orders->where('info->double_type', true);
            } else {
                $orders = $orders->where(function ($q) {
                    $q->whereNull('info->double_type')->orWhere('info->double_type', false);
                });
            }
        }

        if ($filterProductName) {
            $orders = $orders->where('product', 'LIKE', '%' . $productName . '%');
        }

        $page_size = 20;
        if (is_numeric($request->input('page_size', 0)) && $request->input('page_size', 0) > 0) {
            $page_size = $request->input('page_size');
        }

        // Return view
        $o = $this;
        $orders = $orders->orderBy('created_at', 'desc');
        $ordersCount = $o->ordersCount($orders->get());
        $orders = OrderResource::collection($orders->paginate($page_size));

        return [
            'orders_count' => $ordersCount,
            'orders' => $orders,
            'period' => 'oggi',

            'admin' => $admin,

            'filter_date_from' => $fromDate,
            'filter_date_to' => $toDate,

            'filter_network_sent' => $filterNetworkSent,
            'filter_double_type' => $filterDoubleType,

            'filter_product_name' => $productName,

            'page_size' => $page_size,
        ];
    }

    public function filterNotPaginated(Request $request, $admin = false) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        $orders = Order::query();

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

        // Product name filters
        $filterProductName = false;
        $productName = '';
        if ($request->input('filter_product_name', '') != '') {
            $filterProductName = true;
            $productName = $request->input('filter_product_name');
        }

        $admin = $admin && Auth::user()->hasPermission('admin');

        $orders = Order::whereBetween('created_at', [$from, $to]);

        if (!$admin) {
            $orders = $orders->where('user_id', Auth::user()->id);
        }

        if ($filterProductName) {
            $orders = $orders->where('product', 'LIKE', '%' . $productName . '%');
        }

        $page_size = 20;
        if (is_numeric($request->input('page_size', 0)) && $request->input('page_size', 0) > 0) {
            $page_size = $request->input('page_size');
        }

        // Return view
        $o = $this;
        $orders = $orders->orderBy('created_at', 'desc');
        $ordersCount = $o->ordersCount($orders->get());

        return [
            'orders_count' => $ordersCount,
            'orders' => $orders,
            'period' => 'oggi',

            'admin' => $admin,

            'filter_date_from' => $fromDate,
            'filter_date_to' => $toDate,

            'filter_product_name' => $productName,

            'page_size' => $page_size,
        ];
    }

    public function ordersIndex(Request $request) {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        return view('orders')->with($this->filter($request, false));
    }

    public function ordersFilter(Request $request)
    {
        if(!Auth::check()) {
            return redirect()->route('login');
        }
        return view('orders')->with($this->filter($request));
    }

    public function adminOrdersIndex(Request $request)
    {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        return view('orders')->with($this->filter($request, true));
    }

    public function adminOrdersFilter(Request $request)
    {
        if(!Auth::check()) {
            return redirect()->route('login');
        }

        return view('orders')->with($this->filter($request, true));
    }
}
