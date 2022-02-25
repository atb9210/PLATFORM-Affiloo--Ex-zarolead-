<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\third\WarehouseLead;

use App\Settings;

class WarehouseController extends Controller
{
    private $table = 'third_warehouse_leads';

    private function statusValidation()
    {
        return 'in:' . implode(',', WarehouseLead::$statuses);
    }

    private function generateRandomString(int $length = 32): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function interpretConfirmationMessage(string $s, array $context): string
    {
        $result = '';

        $len = strlen($s);
        for ($i = 0; $i < $len; $i += 1) {
            if ($s[$i] === '{' && $i < $len - 1 && $s[$i+1] === '{') {
                $i += 2;
                $var = '';
                $flag = true;
                while (!($s[$i] === '}' && $i < $len -1 && $s[$i+1] === '}')) {
                    $var .= $s[$i];
                    $i += 1;
                    if ($i >= $len) {
                        $result .= '{{' . $var;
                        $flag = false;
                        break;
                    }
                }
                if ($flag) {
                    $i += 1;
                    $var = trim($var);
                    if (isset($context[$var])) {
                        $result .= strval($context[$var]);
                    }
                }
            } else {
                $result .= $s[$i];
            }
        }

        return $result;
    }

    /**
     * Get leads list.
     *
     * @bodyParam filter_date_from string From date (Y-m-d format)
     * @bodyParam filter_date_to string To date (Y-m-d format)
     * @bodyParam filter_product string Leads product's SKU
     *
     * @response {
     *  "data": [leads],
     * }
     */
    public function leads(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to'   => 'nullable|date|date_format:Y-m-d',
            'filter_product'   => 'nullable|string',
        ]);

        $q = WarehouseLead::where('archived', 0);;
        if (isset($params['filter_date_from'])) {
            $q = $q->where('created_at', '>=', $params['filter_date_from'] . " 00:00:00");
        }
        if (isset($params['filter_date_to'])) {
            $q = $q->where('created_at', '<=', $params['filter_date_to'] . " 23:59:59");
        }
        if (isset($params['filter_product'])) {
            $q = $q->where('info->sku', $params['filter_product']);
        }
        $objects = $q->orderBy('created_at', 'desc')->get();

        return response([
            'data' => $objects,
        ]);
    }

    /**
     * Create a lead.
     *
     * @bodyParam first_name string required Lead's first name
     * @bodyParam last_name string required Lead's last name
     * @bodyParam phone string required Lead's phone number
     * @bodyParam notes string Lead's notes
     * @bodyParam info string Lead's info
     * @bodyParam status string Lead's status
     * @bodyParam order_id string Lead's order id
     * @bodyParam product_id string Lead's product id
     * @bodyParam sku string Lead's sku
     * @bodyParam product_name string Lead's product
     * @bodyParam price string Lead's price
     * @bodyParam total string Lead's total
     * @bodyParam quantity string Lead's quantity
     *
     * @response {
     *  "success": true,
     *  "data": lead,
     * }
     */
    public function createLead(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'first_name' => 'required|string|min:1',
            'last_name'  => 'required|string|min:1',
            'phone'      => 'required|string|min:7',
            'notes'      => 'string',
            'info'       => 'json',
            'status'     => 'nullable|string|' . $this->statusValidation(),

            'order_id'     => 'numeric',
            'product_id'   => 'numeric',
            'sku'          => 'string',
            'product_name' => 'string',
            'price'        => 'numeric',
            'total'        => 'numeric',
            'quantity'     => 'numeric',

            'ip'             => 'nullable|string',
            'user_agent'     => 'nullable|string',
            'source_url'     => 'nullable|string',
            'affiliate_id'   => 'nullable|numeric',
            'traffic_source' => 'nullable|string',
        ]);


        // When we have the full name in the first_name field we need to split
        // into first name and last name
        $first_name = $params['first_name'];
        $last_name = $params['last_name'];
        if ($last_name === '.' || trim($last_name) === '') {
            $tmp = explode(' ', $first_name);
            if (count($tmp) > 0) {
                $first_name = $tmp[0];
                $last_name = '';
                for ($j = 1; $j < count($tmp); $j += 1) {
                    $last_name .= ' ' . $tmp[$j];
                }
                $last_name = trim($last_name);
            }
        }
        $params['first_name'] = $first_name;
        $params['last_name'] = $last_name;

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        if (isset($params['order_id'])) {
            $params['info']['order_id'] = $params['order_id'];
        }
        $product = null;
        if (isset($params['product_id'])) {
            $params['info']['product_id'] = $params['product_id'];
            $product = \App\Product::find($params['product_id']);
        }
        if (isset($params['sku'])) {
            $params['info']['sku'] = $params['sku'];
        }
        if (isset($params['product_name'])) {
            $params['info']['product_name'] = $params['product_name'];
        }

        $price = 0.0;
        if (isset($params['price'])) {
            $params['info']['price'] = $params['price'];
            $price = $params['price'];
        }

        $total = 0.0;
        if (isset($params['total'])) {
            $params['info']['total'] = $params['total'];
            $total = $params['total'];
        }

        $quantity = 0.0;
        if (isset($params['quantity'])) {
            $params['info']['quantity'] = $params['quantity'];
            $quantity = $params['quantity'];
        }

        if (isset($params['ip'])) {
            $params['info']['ip'] = $params['ip'];
        }
        if (isset($params['user_agent'])) {
            $params['info']['user_agent'] = $params['user_agent'];
        }
        if (isset($params['source_url'])) {
            $params['info']['source_url'] = $params['source_url'];
        }
        if (isset($params['affiliate_id'])) {
            $params['info']['affiliate_id'] = $params['affiliate_id'];
        }
        if (isset($params['traffic_source'])) {
            $params['info']['traffic_source'] = $params['traffic_source'];
        }

        $params['hash'] = $this->generateRandomString(64);

        // TODO: Check if that hash already exists

        $lead = WarehouseLead::create($params);

        // Check if it's already present a lead with same SKU and phone number
        // in the last 7 days
        $sku = '';
        if (isset($params['sku'])) {
            $sku = $params['sku'];
        }
        if ($sku !== '') {
            $phone = $params['phone'];
            $checkLead = WarehouseLead::where('archived', 0)
                ->where('phone', $phone)
                ->where('created_at', '>=', date('Y-m-d', strtotime('-7 day')) . " 00:00:00")
                ->where('created_at', '<=', date('Y-m-d') . " 23:59:59")
                ->get();
            foreach ($checkLead as $l) {
                if ($l !== null) {
                    if ($l->id != $lead->id && $l->info !== null && isset($l->info['sku']) &&
                        $l->info['sku'] == $sku) {
                        $lead->status = 'duplicate';
                        $lead->save();
                    }
                }
            }
        }

        // TODO: By default create the lead on elogy too
        if ($lead->status !== 'duplicate') {
            $elogy = new \Driverfury\Elogy\Elogy(
                env('ELOGY_API_TOKEN'),
                env('ELOGY_EMAIL'),
                env('ELOGY_PASSWORD'));
            $deliveryParams = [
                'country' => 'IT',
                'total_price' => $total,
                'subtotal_price' => $total,
                'shipping_price' => 0.0,
                'total_tax' => 0.0,
                'tax_included' => 1,
                'gateway' => 'cashondelivery',
                'currency' => 'EUR',
                'customer_first_name' => $params['first_name'],
                'customer_last_name' => $params['last_name'],
                'shipping_phone' => $params['phone'],
                'created_at' => date('YYYY-mm-dd hh:ii'),
                'status' => 'lead',
                'products' => [
                    [
                        'sku' => $sku,
                        'quantity' => $quantity,
                        'subtotal' => $price,
                    ],
                ],
            ];
            $elogyOrder = $elogy->createOrder($deliveryParams);
            if ($elogyOrder !== null) {
                $orderNumber = strval($elogyOrder['order_number']);
                $info = $lead->info;
                $elogyOrder = $elogy->getOrderByNumber($orderNumber);
                if($elogyOrder !== null) {
                    $info['delivery'] = $elogyOrder;
                } else {
                    $info['delivery'] = [
                        'order_number' => $orderNumber,
                    ];
                }
                $lead->info = $info;
                $lead->save();
            }

            // Send confirmation link message via whatsapp
            if ($product !== null) {
                $chatapi = $this->getChatAPIClient();
                $message = null;
                $productInfo = $product->info;
                if ($productInfo === null) {
                    $productInfo = [];
                }
                if (isset($productInfo['confirmation_message'])) {
                    // TODO: More general algorithm to compute confirm/cancel links
                    $confirmLink = env('APP_URL') . '/products/' . $product->id . '/checkout?lid=' . $lead->hash;
                    $cancelLink = env('APP_URL') . '/products/checkout/' . $lead->hash . '/cancel';

                    $msgContext = [
                        'product_name' => $product->name,
                        'confirm_link' => $confirmLink,
                        'cancel_link' => $cancelLink,
                    ];
                    $q = $product->quantity;
                    if ($q === null) {
                        $q = [];
                    }
                    if (isset($q['options']) && $q['options'] !== null) {
                        foreach ($q['options'] as $key => $val) {
                            $price = 0.0;
                            if (isset($val['price']) && $val['price'] !== null) {
                                $price = $val['price'];
                            }
                            $msgContext['price(' . $key . ')'] = $val['price'];
                        }
                    }
                    $message = $this->interpretConfirmationMessage($productInfo['confirmation_message'], $msgContext);
                }
                if ($message !== null && $message !== '') {
                    // TODO: This should be delayed by 10 minutes
                    $chatapi->sendMessage($params['phone'], $message);
                }
            }
        }

        $lead['number'] = $lead->id;
        $lead['network'] = 'warehouse';

        return response([
            'status' => 'success',
            'data' => $lead,
        ]);
    }
    
    /**
     * Update a lead.
     *
     * @urlParam id int required Lead's id
     *
     * @bodyParam first_name string Lead's first name
     * @bodyParam last_name string Lead's last name
     * @bodyParam phone string Lead's phone number
     * @bodyParam notes string Lead's notes
     * @bodyParam info string Lead's info
     * @bodyParam status string Lead's status
     *
     * @response {
     *  "data": lead,
     * }
     */
    public function updateLead($id, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'first_name' => 'string|min:1',
            'last_name'  => 'string|min:1',
            'phone'      => 'nullable|string|min:7',
            'email'      => 'nullable|string|min:4',
            'notes'      => 'string',
            'info'       => 'json',
            'status'     => 'nullable|string|' . $this->statusValidation(),
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        $lead = WarehouseLead::findOrFail($id);
        $lead->update($params);

        return response([
            'data' => $lead,
        ]);
    }

    /**
     * Update a lead.
     *
     * @urlParam hash int required Lead's hash
     *
     * @bodyParam first_name string Lead's first name
     * @bodyParam last_name string Lead's last name
     * @bodyParam phone string Lead's phone number
     * @bodyParam notes string Lead's notes
     * @bodyParam info string Lead's info
     * @bodyParam status string Lead's status
     *
     * @response {
     *  "message": "Lead updated successfully",
     * }
     */
    public function firstUpdateLead($hash, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'first_name' => 'string|min:1',
            'last_name'  => 'string|min:1',
            'phone'      => 'nullable|string|min:7',
            'email'      => 'nullable|string|min:4',
            'notes'      => 'string',
            'info'       => 'json',
            'status'     => 'nullable|string|' . $this->statusValidation(),
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        $lead = WarehouseLead::findByHash($hash);
        if ($lead === null) {
            return response([
                'status' => 'fail',
                'message' => 'Lead not found',
            ], 404);
        }
        $params['hash'] = $this->generateRandomString(64);
        $lead->update($params);

        return response([
            'message' => 'Lead updated successfully',
        ]);
    }
    
    /**
     * Get lead info.
     *
     * @urlParam hash int required Lead's hash
     *
     * @response {
     *  "data": lead,
     * }
     */
    public function getLeadInfo($hash, Request $request)
    {
        // TODO: Authorization

        $lead = WarehouseLead::findByHash($hash);
        if ($lead === null) {
            return response([
                'status' => 'fail',
                'message' => 'Lead not found',
            ], 404);
        }

        $info = $lead->info;
        if ($info === null) {
            $info = [];
        }

        $shipping = [];
        if (isset($info['shipping']) && $info['shipping'] !== null) {
            foreach ($info['shipping'] as $key => $value) {
                $shipping[$key] = $value;
            }
        }

        $quantity = null;
        if (isset($info['quantity'])) {
            $quantity = $info['quantity'];
        }

        $total = 0;
        if (isset($info['total'])) {
            $total = $info['total'];
        }

        $productName = '';
        if (isset($info['product_name'])) {
            $productName = $info['product_name'];
        }

        $products = [];
        $accessories = [];
        if (isset($info['products']) && $info['products'] !== null) {
            $products = $info['products'];
        }
        if (isset($info['accessories']) && $info['accessories'] !== null) {
            $accessories = $info['accessories'];
        }

        return response([
            'data' => [
                'id' => $lead->hash,
                'hash' => $lead->hash,
                'number' => $lead->id,
                'first_name' => $lead->first_name,
                'last_name' => $lead->last_name,
                'phone' => $lead->phone,
                'product_name' => $productName,
                'quantity' => $quantity,
                'total' => $total,
                'products' => $products,
                'accessories' => $accessories,
                'shipping' => $shipping,
                'created_at' => $lead->created_at,
            ],
        ]);
    }
    

    /**
     * Update a lead's address.
     *
     * @urlParam hash int required Lead's hash
     *
     * @bodyParam street_address string required Street address
     * @bodyParam city string required City
     * @bodyParam province string required Province
     * @bodyParam state string required State
     * @bodyParam country string required Country
     * @bodyParam postcode string required Postal code
     *
     * @response {
     *  "status": "success",
     *  "message": "Lead's address updated',
     * }
     */
    public function updateLeadAddress($hash, Request $request)
    {
        // Param validation
        $params = $request->validate([
            'street_address' => 'required|string',
            'city'           => 'required|string',
            'province'       => 'required|string',
            'state'          => 'required|string',
            'country'        => 'required|string',
            'postcode'       => 'required|string',
        ]);

        $lead = WarehouseLead::findByHash($hash);
        if ($lead === null) {
            return response([
                'status' => 'fail',
                'message' => 'Lead not found',
            ], 404);
        }

        // TODO: Check time difference now() - lead.created_at

        $info = $lead->info;
        if ($info === null) {
            $info = [];
        }
        if (!isset($info['shipping']) || $info['shipping'] === null || empty($info['shipping'])) {
            $info['shipping'] = $params;
        } else {
            foreach ($params as $key => $value) {
                $info['shipping'][$key] = $value;
            }
        }
        $lead->info = $info;
        $lead->save();

        if (isset($info['delivery']['order_number'])) {
            $orderNumber = strval($info['delivery']['order_number']);
            $elogy = new \Driverfury\Elogy\Elogy(
                env('ELOGY_API_TOKEN'),
                env('ELOGY_EMAIL'),
                env('ELOGY_PASSWORD'));
            $elogyOrder = $elogy->getOrderByNumber($orderNumber);
            if ($elogyOrder !== null) {
                $orderId = $elogyOrder['id'];
                $deliveryParams = [
                    'shipping_first_name' => $lead->first_name,
                    'shipping_last_name' => $lead->last_name,
                    'shipping_phone' => $lead->phone,
                    'shipping_address1' => $params['street_address'],
                    'shipping_address2' => '',
                    'shipping_city' => $params['city'],
                    'shipping_zip' => $params['postcode'],
                    'shipping_province_code' => $params['province'],
                    'shipping_country_code' => $params['country'],
                ];
                if ($elogy->updateOrder(strval($orderId), $deliveryParams)) {
                    $delivery = $elogy->getOrderByNumber($orderNumber);
                    if ($delivery !== null) {
                        $info['delivery'] = $delivery;
                        $lead->info = $info;
                        $lead->save();
                    }
                }
            }
        }

        return response([
            'status' => 'success',
            'message' => 'Lead\'s address updated',
        ]);
    }

    /**
     * Update a lead's status and total.
     *
     * @urlParam hash int required Lead's hash
     *
     * @bodyParam first_name string First name
     * @bodyParam last_name string Last
     * @bodyParam phone string Phone number
     *
     * @response {
     *  "status": "success",
     *  "message": "Lead updated',
     * }
     */
    public function publicUpdateLead($hash, Request $request)
    {
        // Param validation
        $params = $request->validate([
            'first_name' => 'nullable|string',
            'last_name'  => 'nullable|string',
            'phone'      => 'nullable|string',
        ]);

        $lead = WarehouseLead::findByHash($hash);
        if ($lead === null) {
            return response([
                'status' => 'fail',
                'message' => 'Lead not found',
            ], 404);
        }

        // TODO: Check time difference now() - lead.created_at

        if (isset($params['first_name']) && $params['first_name'] !== null) {
            $lead->first_name = $params['first_name'];
        }
        if (isset($params['last_name']) && $params['last_name'] !== null) {
            $lead->last_name = $params['last_name'];
        }
        if (isset($params['phone']) && $params['phone'] !== null) {
            $lead->phone = $params['phone'];
        }

        $lead->save();

        return response([
            'status' => 'success',
            'message' => 'Lead updated',
        ]);
    }

    /**
     * Update a lead's status and total.
     *
     * @urlParam hash int required Lead's hash
     *
     * @bodyParam status string required Status
     * @bodyParam total string required Total
     * @bodyParam payment_method string required Payment method
     *
     * @response {
     *  "status": "success",
     *  "message": "Lead updated',
     * }
     */
    public function completeLead($hash, Request $request)
    {
        // Param validation
        $params = $request->validate([
            'status'         => 'required|string|' . $this->statusValidation(),
            'total'          => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'push'           => 'nullable|string',
        ]);

        $lead = WarehouseLead::findByHash($hash);
        if ($lead === null) {
            return response([
                'status' => 'fail',
                'message' => 'Lead not found',
            ], 404);
        }

        // TODO: Check time difference now() - lead.created_at

        $info = $lead->info;
        if ($info === null) {
            $info = [];
        }
        $info['paid'] = true;
        $info['total'] = $params['total'];
        $info['payment_method'] = $params['payment_method'];
        $info['gateway'] = $params['payment_method'];
        $lead->info = $info;
        $lead->status = $params['status'];
        $lead->save();

        if (isset($info['delivery']['order_number'])) {
            $orderNumber = strval($info['delivery']['order_number']);
            $elogy = new \Driverfury\Elogy\Elogy(
                env('ELOGY_API_TOKEN'),
                env('ELOGY_EMAIL'),
                env('ELOGY_PASSWORD'));
            $elogyOrder = $elogy->getOrderByNumber($orderNumber);
            if ($elogyOrder !== null) {
                $orderId = $elogyOrder['id'];
                $deliveryParams = [
                    'gateway' => $params['payment_method'],
                ];
                if (isset($params['push']) && $params['push'] !== null) {
                    if ($params['push'] === 'auto') {
                        $deliveryParams['transform_to_order'] = 1;
                        $deliveryParams['completed_at'] = (new \DateTime())->format(\DateTime::ISO8601);
                    }
                }
                if ($elogy->updateOrder(strval($orderId), $deliveryParams)) {
                    $delivery = $elogy->getOrderByNumber($orderNumber);
                    if ($delivery !== null) {
                        $info['delivery'] = $delivery;
                        $lead->info = $info;
                        $lead->save();
                    }
                }
            }
        }

        return response([
            'status' => 'success',
            'message' => 'Lead updated',
        ]);
    }

    /**
     * Delete a lead.
     *
     * @urlParam hash int required Lead's public hash
     *
     * @response {
     *  "status": "success",
     *  "message": "Lead successfully deleted",
     * }
     */
    public function publicDeleteLead($hash, Request $request)
    {
        // TODO: Authorization

        $lead = WarehouseLead::findByHash($hash);
        if ($lead === null) {
            return response([
                'status' => 'fail',
                'message' => 'Lead not found',
            ], 404);
        }

        $info = $lead->info;
        if ($info !== null && isset($info['delivery']) && $info['delivery'] !== null) {
            $orderId = null;
            $orderNumber = null;

            if (isset($info['delivery']['id'])) {
                $orderId = $info['delivery']['id'];
            }
            if (isset($info['delivery']['order_number'])) {
                $orderNumber = $info['delivery']['order_number'];
            }

            if (($orderId !== null && $orderId !== '') || ($orderNumber !== null && $orderNumber !== '')) {
                $elogy = new \Driverfury\Elogy\Elogy(
                    env('ELOGY_API_TOKEN'),
                    env('ELOGY_EMAIL'),
                    env('ELOGY_PASSWORD'));
                if (($orderId === null || $orderId === '') && $orderNumber !== null && $orderNumber !== '') {
                    $order = $elogy->getOrderByNumber($orderNumber);
                    if ($order !== null && isset($order['id'])) {
                        $orderId = $order['id'];
                    }
                }
                if ($orderId !== null && $orderId !== '') {
                    $elogy->deleteOrder($orderId);
                }
            }
        }

        //$lead->delete();
        $lead->status = 'cancelled';
        $lead->archived = 1;
        $lead->save();

        return response([
            'status' => 'success',
            'message' => 'Lead successfully deleted',
        ]);
    }

    /**
     * Delete a lead.
     *
     * @urlParam id int required Lead's id
     *
     * @response {
     *  "message": "Lead successfully deleted",
     * }
     */
    public function destroyLead($id, Request $request)
    {
        // TODO: Authorization

        $lead = WarehouseLead::findOrFail($id);

        $info = $lead->info;
        if ($info !== null && isset($info['delivery']) && $info['delivery'] !== null) {
            $orderId = null;
            $orderNumber = null;

            if (isset($info['delivery']['id'])) {
                $orderId = $info['delivery']['id'];
            }
            if (isset($info['delivery']['order_number'])) {
                $orderNumber = $info['delivery']['order_number'];
            }

            if (($orderId !== null && $orderId !== '') || ($orderNumber !== null && $orderNumber !== '')) {
                $elogy = new \Driverfury\Elogy\Elogy(
                    env('ELOGY_API_TOKEN'),
                    env('ELOGY_EMAIL'),
                    env('ELOGY_PASSWORD'));
                if (($orderId === null || $orderId === '') && $orderNumber !== null && $orderNumber !== '') {
                    $order = $elogy->getOrderByNumber($orderNumber);
                    if ($order !== null && isset($order['id'])) {
                        $orderId = $order['id'];
                    }
                }
                if ($orderId !== null && $orderId !== '') {
                    $elogy->deleteOrder($orderId);
                }
            }
        }

        //$lead->delete();
        $lead->archived = 1;
        $lead->save();

        return response([
            'message' => 'Lead successfully deleted',
        ]);
    }

    /**
     * Get stock products list.
     *
     * @response {
     *  "data": [products],
     * }
     */
    public function products()
    {
        // TODO: Authorization

        $elogy = new \Driverfury\Elogy\Elogy(
            env('ELOGY_API_TOKEN'),
            env('ELOGY_EMAIL'),
            env('ELOGY_PASSWORD'));
        $objects = $elogy->getProducts();

        return response([
            'data' => $objects,
        ]);
    }

    /**
     * Get delivery (elogy).
     *
     * @urlParam lead_id string Lead's id
     *
     * @response {
     *  "data": [delivery],
     * }
     */
    public function getDelivery($lead_id, Request $request)
    {
        // TODO: Authorization

        $lead = WarehouseLead::findOrFail($lead_id);

        $info = $lead->info;
        if ($info === null) {
            $info = [];
        }

        $delivery = null;
        if (isset($info['delivery']) && isset($info['delivery']['order_number'])) {
            $elogy = new \Driverfury\Elogy\Elogy(
                env('ELOGY_API_TOKEN'),
                env('ELOGY_EMAIL'),
                env('ELOGY_PASSWORD'));
            $delivery = $elogy->getOrderByNumber($info['delivery']['order_number']);
            $info['delivery'] = $delivery;
        }

        if (!isset($info['delivery'])) {
            $info['delivery'] = [];
        }

        if (!isset($info['delivery']['ip']) && isset($info['ip'])) {
            $info['delivery']['ip'] = $info['ip'];
        }
        if (!isset($info['delivery']['user_agent']) && isset($info['user_agent'])) {
            $info['delivery']['user_agent'] = $info['user_agent'];
        }
        if (!isset($info['delivery']['source_url']) && isset($info['source_url'])) {
            $info['delivery']['source_url'] = $info['source_url'];
        }
        if (!isset($info['delivery']['affiliate_id']) && isset($info['affiliate_id'])) {
            $info['delivery']['affiliate_id'] = $info['affiliate_id'];
        }
        if (!isset($info['delivery']['traffic_source']) && isset($info['traffic_source'])) {
            $info['delivery']['traffic_source'] = $info['traffic_source'];
        }

        $lead->info = $info;
        $lead->save();

        return response([
            'data' => $info['delivery'],
        ]);
    }

    /**
     * Create delivery (elogy).
     *
     * @bodyParam lead_id string Lead's id
     * @bodyParam params json required Delivery's params
     *
     * @response {
     *  "message": "Order pushed",
     * }
     */
    public function createDelivery(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'lead_id' => 'nullable',
            'params'  => 'json|required',
        ]);

        $deliveryParams = json_decode($params['params'], true);

        $elogy = new \Driverfury\Elogy\Elogy(
            env('ELOGY_API_TOKEN'),
            env('ELOGY_EMAIL'),
            env('ELOGY_PASSWORD'));
        $elogyOrder = $elogy->createOrder($deliveryParams);
        if ($elogyOrder === null) {
            if (isset($params['lead_id']) && $params['lead_id'] !== null) {
                $leadId = $params['lead_id'];
                $lead = WarehouseLead::findOrFail($leadId);
                $info = $lead->info;
                $info['delivery'] = null;
                $lead->info = $info;
                $lead->save();
            }

            return response([
                'message' => 'Cannot push the order',
            ], 500);
        }

        if (isset($params['lead_id']) && $params['lead_id'] !== null) {
            $leadId = $params['lead_id'];

            $elogyOrder = $elogy->getOrderByNumber(strval($elogyOrder['order_number']));
            if($elogyOrder !== null) {
                $lead = WarehouseLead::findOrFail($leadId);
                $info = $lead->info;
                $info['delivery'] = $elogyOrder;
                $lead->info = $info;
                $lead->save();
            }

            return response([
                'message' => 'Order pushed',
                'data' => $lead,
            ]);
        }

        return response([
            'message' => 'Order pushed',
        ]);
    }

    /**
     * Update delivery (elogy).
     *
     * @bodyParam lead_id string Lead's id
     * @bodyParam params json required Delivery's params
     *
     * @response {
     *  "message": "Delivery updated",
     * }
     */
    public function updateDelivery(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'lead_id' => 'required|exists:' . $this->table . ',id',
            'params'  => 'json|required',
        ]);

        $lead = WarehouseLead::findOrFail($params['lead_id']);
        $deliveryParams = json_decode($params['params'], true);

        $info = $lead->info;
        if ($info !== null && isset($info['delivery']['order_number'])) {
            $orderNumber = strval($info['delivery']['order_number']);
            $elogy = new \Driverfury\Elogy\Elogy(
                env('ELOGY_API_TOKEN'),
                env('ELOGY_EMAIL'),
                env('ELOGY_PASSWORD'));
            $elogyOrder = $elogy->getOrderByNumber($orderNumber);
            if ($elogyOrder !== null) {
                $orderId = $elogyOrder['id'];
                if ($elogy->updateOrder(strval($orderId), $deliveryParams)) {
                    $delivery = $elogy->getOrderByNumber($orderNumber);
                    if ($delivery !== null) {
                        $info['delivery'] = $delivery;
                        $lead->info = $info;
                        $lead->save();
                    }
                }
            }
        }

        return response([
            'message' => 'Delivery updated',
        ]);
    }

    private function getStripeClient()
    {
        $settings = Settings::get();
        $apiKey = env('STRIPE_API_SECRET', '');
        if (isset($settings['stripe']['api_secret_key'])) {
            $apiKey = $settings['stripe']['api_secret_key'];
        }
        $stripe = new \Stripe\StripeClient($apiKey);
        return $stripe;
    }

    private function getChatAPIClient()
    {
        $settings = Settings::get();
        $apiInstance = env('CHATAPI_INSTANCE', '');
        $apiToken = env('CHATAPI_TOKEN', '');
        if (isset($settings['chatapi']['api_instance'])) {
            $apiInstance = $settings['chatapi']['api_instance'];
        }
        if (isset($settings['chatapi']['api_token'])) {
            $apiToken = $settings['chatapi']['api_token'];
        }
        $chatapi = new \Driverfury\ChatAPI\ChatAPI($apiInstance, $apiToken);
        return $chatapi;
    }

    /**
     * Lead checkout (online payment via Stripe).
     *
     * @urlParam hash required int Lead's id
     */
    public function checkout($hash, Request $request)
    {
        $lead = WarehouseLead::findByHash($hash);
        if ($lead === null) {
            $lead = \App\third\DropinLead::findByHash($hash);
            if ($lead === null) {
                return response([
                    'status' => 'fail',
                    'message' => 'Lead not found',
                ], 404);
            }
        }

        $info = $lead->info;
        if ($info === null) {
            $info = [];
        }

        $line_items = [];
        $products = [];
        if ($info !== null && isset($info['products']) && $info['products'] !== null) {
            $products = $info['products'];
        }

        $stripeKey = '';
        $settings = \App\Settings::get();
        if (isset($settings['stripe']['api_public_key'])) {
            $stripeKey = $settings['stripe']['api_public_key'];
        }

        return view('payment')->with([
            'lead_id' => $lead->hash,
            'products' => $products,
            'stripe_key' => $stripeKey,
        ]);

        // TODO: old code
        /*
        $lead = WarehouseLead::findOrFail($lead_id);
        $info = $lead->info;
        if ($info === null) {
            $info = [];
        }

        // Param validation
        $params = $request->validate([
            'success_url' => 'required|string|url',
            'cancel_url'  => 'required|string|url',
        ]);

        $cancelUrl = url()->previous();
        if (isset($params['cancel_url']) && $params['cancel_url'] != '') {
            $cancelUrl = $params['cancel_url'];
        }
        $successUrl = url()->previous();
        if (isset($params['success_url']) && $params['success_url'] != '') {
            $successUrl = $params['success_url'];
        }

        $handlerParams = [
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ];
        $handlerUrl = url('/leads/' . strval($lead_id) . '/checkout_completed?session_id={CHECKOUT_SESSION_ID}&' . http_build_query($handlerParams));

        $line_items = [];
        $products = [];
        if ($info !== null && isset($info['products']) && $info['products'] !== null) {
            $products = $info['products'];
        }
        foreach ($products as $p) {
            $name = $p['name'];
            $price = intval(floatval($p['price'])*100);
            $quantity = intval($p['quantity']);
            $line_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $price,
                    'product_data' => [
                        'name' => $name,
                    ],
                ],
                'quantity' => $quantity,
            ];
        }

        $stripe = $this->getStripeClient();
        $session = $stripe->checkout->sessions->create([
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => $handlerUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'lead_id' => $lead->id,
            ],
        ]);
        return redirect($session->url);
        */
    }

    /**
     * Completed checkout (online payment via Stripe).
     *
     * @urlParam lead_id required int Lead's id
     *
     * @bodyParam session_id required string Stripe session id
     * @bodyParam success_url required string Success URL (Thank you page)
     * @bodyParam cancel_url required string Cancel URL
     */
    public function checkoutCompleted($lead_id, Request $request)
    {
        $lead = WarehouseLead::findOrFail($lead_id);
        $info = $lead->info;

        // Param validation
        $params = $request->validate([
            'session_id'  => 'required|string',
            'success_url' => 'required|string|url',
            'cancel_url'  => 'required|string|url',
        ]);

        $sessionId = $params['session_id'];
        $cancelUrl = url()->previous();
        if (isset($params['cancel_url']) && $params['cancel_url'] != '') {
            $cancelUrl = $params['cancel_url'];
        }
        $successUrl = url()->previous();
        if (isset($params['success_url']) && $params['success_url'] != '') {
            $successUrl = $params['success_url'];
        }

        $redirectUrl = $cancelUrl;
        $session = null;
        try {
            $stripe = $this->getStripeClient();
            $session = $stripe->checkout->sessions->retrieve($sessionId, []);
            if ($session->status === 'complete' && $session->payment_status === 'paid') {
                $redirectUrl = $successUrl;
                $info['paid'] = true;
            }
        } catch (Exception $e) {
            $session = null;
        }
        if ($session !== null) {
            $info['stripe'] = [
                'session_id' => $session->id,
                'customer_id' => $session->customer,
                'payment_intent_id' => $session->payment_intent,
            ];
        }
        $lead->info = $info;
        $lead->save();

        return redirect($redirectUrl);
    }

    // TODO: Move this somewhere else??
    private function getCurrencyValue(string $s): float
    {
        $s = str_replace(',', '.', $s);
        return floatval($s);
    }

    /**
     * Get payment intent.
     *
     * @urlParam product_id required int Product's id
     *
     * @bodyParam lead required numeric Lead's hash
     * @bodyParam quantity required string Quantity selected
     */
    public function getPaymentIntentProduct($product_id, Request $request)
    {
        // Param validation
        $params = $request->validate([
            'lead'  => 'required|string',
            'quantity'  => 'required|string',
        ]);

        $lead = WarehouseLead::findByHash($params['lead']);
        if ($lead === null) {
            return response([
                'status' => 'fail',
                'message' => 'Lead not found',
            ], 404);
        }

        $product = \App\Product::findOrFail($product_id);
        $quantity = strval($params['quantity']);

        $qty = $product->quantity;
        if ($qty === null) {
            $qty = [];
        }

        $amount = 0.0;
        if (isset($qty['options'][$quantity]['price'])) {
            $amount = $this->getCurrencyValue($qty['options'][$quantity]['price']);
        }

        $stripe = $this->getStripeClient();
        $intent = $stripe->paymentIntents->create([
            'amount' => intval($amount*100),
            'currency' => 'eur',
        ]);

        $info = $lead->info;
        if ($info === null) {
            $info = [];
        }
        $info['price'] = $amount;
        $info['total'] = $amount;
        $info['quantity'] = $quantity;
        $lead->info = $info;
        $lead->save();

        return response([
            'data' => [
                'client_secret' => $intent->client_secret,
            ],
        ]);
    }

    /**
     * Get payment intent (custom price).
     *
     * @bodyParam amount required float Amount
     */
    public function getPaymentIntent(Request $request)
    {
        // Param validation
        $params = $request->validate([
            'amount'  => 'required|numeric',
        ]);

        $amount = floatval($params['amount']);

        $stripe = $this->getStripeClient();
        $intent = $stripe->paymentIntents->create([
            'amount' => intval($amount*100),
            'currency' => 'eur',
        ]);

        return response([
            'data' => [
                'client_secret' => $intent->client_secret,
            ],
        ]);
    }

    /**
     * Send message
     *
     * @bodyParam phone required string Phone number
     * @bodyParam message required string Message
     */
    public function sendMessage(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'phone'   => 'required|string',
            'message' => 'required|string',
        ]);

        $chatapi = $this->getChatAPIClient();
        $result = $chatapi->sendMessage($params['phone'], $params['message']);

        if (!$result) {
            return response([
                'status' => 'fail',
                'message' => 'Message not sent',
            ], 500);
        }

        return response([
            'status' => 'success',
            'message' => 'Message sent',
        ]);
    }
}
