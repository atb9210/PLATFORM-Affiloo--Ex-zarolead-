<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\third\EcommerceCart;
use App\third\EcommerceSettings;

class EcommerceController extends Controller
{
    private $statusValidation = 'in:confirmed,no answer,cancelled,fake,duplicate,to recall,to do';

    private function getClient()
    {
        return new \Automattic\WooCommerce\Client(
            env('WOOCOMMERCE_URL'),
            env('WOOCOMMERCE_API_KEY'),
            env('WOOCOMMERCE_API_SECRET')
        );
    }

    private function getRequestAllPages($url, $params = [])
    {
        $objects = [];

        $woo = $this->getClient();
        $wooParams = array_merge($params, [
            'per_page' => 100,
            'page' => 1,
        ]);
        do {
            $otherObjects = $woo->get($url, $wooParams);
            foreach ($otherObjects as $obj) {
                $objects[] = $obj;
            }
            $wooParams['page'] += 1;
        } while(count($otherObjects) > 0);

        return $objects;
    }

    /**
     * Get customers list.
     *
     * @response {
     *  "data": [customers],
     * }
     */
    public function customers()
    {
        // TODO: Authorization

        $objects = $this->getRequestAllPages('customers');

        return response([
            'data' => $objects,
        ]);
    }

    /**
     * Get orders list.
     *
     * @response {
     *  "data": [orders],
     * }
     */
    public function orders(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'date',
            'filter_date_to' => 'date',
        ]);

        $wooParams = [
            'orderby' => 'date',
            'order' => 'desc',
        ];

        if (isset($params['filter_date_from'])) {
            $d = date('c', strtotime($params['filter_date_from']));
            $wooParams['after'] = $d;
        }
        if (isset($params['filter_date_to'])) {
            $d = date('c', strtotime($params['filter_date_to']));
            $d = str_replace('00:00:00', '23:59:59', $d);
            $wooParams['before'] = $d;
        }

        $objects = $this->getRequestAllPages('orders', $wooParams);

        return response([
            'data' => $objects,
        ]);
    }

    /**
     * Get products list.
     *
     * @response {
     *  "data": [products],
     * }
     */
    public function products()
    {
        // TODO: Authorization

        $objects = $this->getRequestAllPages('products');

        return response([
            'data' => $objects,
        ]);
    }
    
    /**
     * Get product.
     *
     * @response {
     *  "data": product,
     * }
     */
    public function product($id)
    {
        // TODO: Authorization

        $woo = $this->getClient();
        $object = $woo->get('products/' . strval($id));

        return response([
            'data' => $object,
        ]);
    }

    /**
     * Get product's variations.
     *
     * @response {
     *  "data": [product_variations],
     * }
     */
    public function productVariations($id)
    {
        // TODO: Authorization

        $objects = $this->getRequestAllPages('products/' . strval($id) . '/variations');

        return response([
            'data' => $objects,
        ]);
    }

    /**
     * Update product.
     *
     * @urlParam id int required Product's id
     *
     * @bodyParam params json reqruired Product's params
     *
     * @response {
     *  "data": product,
     * }
     */
    public function updateProduct($id, Request $request)
    {
        // Param validation
        $params = $request->validate([
            'params' => 'json|required',
        ]);

        $params = json_decode($params['params'], true);

        $woo = $this->getClient();
        $product = $woo->put('products/' . strval($id), $params);

        return response([
            'data' => $product,
        ]);
    }

    /**
     * Update product's variation
     *
     * @urlParam pid int required Product's id
     * @urlParam vid int required Variation's id
     *
     * @bodyParam params json reqruired Variation's params
     *
     * @response {
     *  "data": variation,
     * }
     */
    public function updateProductVariation($pid, $vid, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'params' => 'json|required',
        ]);

        $params = json_decode($params['params'], true);

        $woo = $this->getClient();
        $variation = $woo->put('products/' . strval($pid) . '/variations/' . strval($vid), $params);

        return response([
            'data' => $variation,
        ]);
    } 

    /**
     * Create order.
     *
     * @bodyParam params json reqruired Order's params
     *
     * @response {
     *  "data": order,
     * }
     */
    public function createOrder(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'params' => 'json|required',
        ]);

        $params = json_decode($params['params'], true);

        $woo = $this->getClient();
        $order = $woo->post('orders', $params);

        return response([
            'data' => $order,
        ]);
    }

    /**
     * Update order.
     *
     * @urlParam id int required Order's id
     *
     * @bodyParam params json reqruired Order's params
     *
     * @response {
     *  "data": order,
     * }
     */
    public function updateOrder($id, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'params' => 'json|required',
        ]);

        $params = json_decode($params['params'], true);

        $woo = $this->getClient();
        $order = $woo->put('orders/' . strval($id), $params);

        return response([
            'data' => $order,
        ]);
    }

    /**
     * Push the order to elogy.
     *
     * @urlParam id int required Order's id
     *
     * @response {
     *  "message": "Order pushed",
     * }
     */
    public function pushOrder($id, Request $request)
    {
        // TODO: Authorization

        $woo = $this->getClient();
        $order = $woo->get('orders/' . $id);
        $order = json_decode(json_encode($order), true);

        $totalPrice = floatval($order['total']);
        $shippingTotal = floatval($order['shipping_total']);
        $taxTotal = floatval($order['total_tax']);
        $subtotalPrice = $totalPrice - $shippingTotal - $taxTotal;

        $gateway = 'online';
        if ($order['payment_method'] === 'cod') {
            $gateway = 'cashondelivery';
        }

        $products = [];
        foreach ($order['line_items'] as $item) {
            $products[] = [
                'sku' => $item['sku'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['total'],
            ];
        }

        $params = [
            'order_name' => '#' . $order['id'],
            'order_number' => $order['id'],
            'country' => 'IT',
            'total_price' => $order['total'],
            'subtotal_price' => $subtotalPrice,
            'shipping_price' => $order['shipping_total'],
            'total_tax' => $order['total_tax'],
            'tax_included' => 1,
            'gateway' => $gateway,
            'currency' => 'EUR',
            'customer_first_name' => $order['billing']['first_name'],
            'customer_last_name' => $order['billing']['last_name'],
            'shipping_first_name' => $order['shipping']['first_name'],
            'shipping_last_name' => $order['shipping']['last_name'],
            'shipping_address1' => $order['shipping']['address_1'],
            'shipping_address2' => $order['shipping']['address_2'],
            'shipping_city' => $order['shipping']['city'],
            'shipping_zip' => $order['shipping']['postcode'],
            'shipping_province_code' => $order['shipping']['state'],
            'shipping_country_code' => $order['shipping']['country'],
            'shipping_phone' => $order['billing']['phone'],
            'shipping_email' => $order['billing']['email'],
            'shipping_notes' => $order['customer_note'],
            'created_at' => $order['date_created'],
            'completed_at' => '',
            'products' => $products,
            'params' => [],
        ];

        $elogy = new \Driverfury\Elogy\Elogy(
            env('ELOGY_API_TOKEN'),
            env('ELOGY_EMAIL'),
            env('ELOGY_PASSWORD'));
        if (!$elogy->createOrder($params)) {
            return response([
                'message' => 'Cannot push the order',
            ], 500);
        }

        return response([
            'message' => 'Order pushed',
        ]);
    } 

    /**
     * Get payment methods list.
     *
     * @response {
     *  "data": [payment_methods],
     * }
     */
    public function paymentMethods(Request $request)
    {
        // TODO: Authorization

        $woo = $this->getClient();
        $objects = $woo->get('payment_gateways');

        // Fees
        $settings = EcommerceSettings::get();
        for ($i = 0; $i < count($objects); $i += 1) {
            $objects[$i]->fixed_fee = 0.00;
            $objects[$i]->flat_fee = 0.00;

            if (isset($settings['fees'][$objects[$i]->id]['fixed'])) {
                $objects[$i]->fixed_fee = $settings['fees'][$objects[$i]->id]['fixed'];
            }
            if (isset($settings['fees'][$objects[$i]->id]['flat'])) {
                $objects[$i]->flat_fee = $settings['fees'][$objects[$i]->id]['flat'];
            }
        }

        return response([
            'data' => $objects,
        ]);
    }

    /**
     * Update payment method's settings.
     *
     * @urlParam id int required Payment method's id
     *
     * @bodyParam settings json reqruired Settings
     *
     * @response {
     *  "message": "Fees updated",
     * }
     */
    public function updatePaymentMethodFees($id, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'fixed' => 'required|numeric',
            'flat' => 'required|numeric',
        ]);

        // Update fee
        $settings = EcommerceSettings::get();
        if (!isset($settings['fees'])) {
            $settings['fees'] = [];
        }
        if (!isset($settings['fees'][$id])) {
            $settings['fees'][$id] = [
                'fixed' => 0.0,
                'flat'  => 0.0,
            ];
        }
        $settings['fees'][$id] = [
            'fixed' => floatval($params['fixed']),
            'flat'  => floatval($params['flat']),
        ];
        EcommerceSettings::set($settings);

        return response([
            'message' => 'Fees updated',
        ]);
    }

    /**
     * Get advertising csv
     *
     * @response {
     *  "data": "csv_url"
     * }
     */
    public function getAdvertisingCsv(Request $request)
    {
        // TODO: Authorization

        $csv = EcommerceSettings::getK('advertising');
        if ($csv === null) {
            $csv = '';
        }

        return response([
            'data' => $csv,
        ]);
    }

    /**
     * Update advertising csv
     *
     * @response {
     *  "message": "Advertising CSV updated",
     * }
     */
    public function updateAdvertisingCsv(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'csv' => 'required|url',
        ]);

        EcommerceSettings::setK('advertising', $params['csv']);

        return response([
            'message' => 'Advertising CSV updated',
        ]);
    }

    /**
     * Get shipping csv
     *
     * @response {
     *  "data": "csv_url"
     * }
     */
    public function getShippingCsv(Request $request)
    {
        // TODO: Authorization

        $csv = EcommerceSettings::getK('shipping');
        if ($csv === null) {
            $csv = '';
        }

        return response([
            'data' => $csv,
        ]);
    }

    /**
     * Update shipping csv
     *
     * @response {
     *  "message": "Advertising CSV updated",
     * }
     */
    public function updateShippingCsv(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'csv' => 'required|url',
        ]);

        EcommerceSettings::setK('shipping', $params['csv']);

        return response([
            'message' => 'Shipping CSV updated',
        ]);
    }
    
    /**
     * Get extra cost csv
     *
     * @response {
     *  "data": "csv_url"
     * }
     */
    public function getExtraCostCsv(Request $request)
    {
        // TODO: Authorization

        $csv = EcommerceSettings::getK('extra_cost');
        if ($csv === null) {
            $csv = '';
        }

        return response([
            'data' => $csv,
        ]);
    }

    /**
     * Update extra cost csv
     *
     * @response {
     *  "message": "Advertising CSV updated",
     * }
     */
    public function updateExtraCostCsv(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'csv' => 'required|url',
        ]);

        EcommerceSettings::setK('extra_cost', $params['csv']);

        return response([
            'message' => 'Extra cost CSV updated',
        ]);
    }

    /**
     * Get carts list.
     *
     * @response {
     *  "data": [carts],
     * }
     */
    public function carts(Request $request)
    {
        // TODO: Authorization

        $carts = EcommerceCart::sync();

        return response([
            'data' => $carts,
        ]);
    }

    /**
     * Update a cart.
     *
     * @urlParam id int required Cart's id
     *
     * @bodyParam first_name string Cart's customer first name
     * @bodyParam last_name string Cart's customer last name
     * @bodyParam email string Cart's customer email
     * @bodyParam phone string Cart's customer phone number
     * @bodyParam notes string Cart's notes
     * @bodyParam info string Cart's info
     * @bodyParam status string Cart's status
     *
     * @response {
     *  "data": cart,
     * }
     */
    public function updateCart($id, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'first_name' => 'string|min:1',
            'last_name'  => 'string|min:1',
            'email'      => 'string|min:7',
            'phone'      => 'string|min:7',
            'notes'      => 'string',
            'info'       => 'json',
            'status'     => 'string|' . $this->statusValidation,
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        $cart = EcommerceCart::findOrFail($id);
        $cart->update($params);

        return response([
            'data' => $cart,
        ]);
    }

    /**
     * Delete a cart.
     *
     * @urlParam id int required Cart's id
     *
     * @response {
     *  "message": "Cart successfully deleted",
     * }
     */
    public function destroyCart($id, Request $request)
    {
        // TODO: Authorization

        $cart = EcommerceCart::findOrFail($id);
        $cart->delete();

        return response([
            'message' => 'Cart successfully deleted',
        ]);
    }
}
