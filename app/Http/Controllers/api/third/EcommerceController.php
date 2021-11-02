<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Driverfury\Worldfilia\Worldfilia;

use App\third\EcommerceSettings;

class EcommerceController extends Controller
{
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

    private function decodeCartObj($s)
    {
        $type = 'x';
        $len = 0;
        $val = '';

        if(strlen($s) >= 3)
        {
            $type = $s[0];
            $s = substr($s, 2);

            $i = 0;
            while (is_numeric($s[$i])) {
                $i += 1;
            }
            $len = intval(substr($s, 0, $i));

            if ($type == 's') {
                $s = substr($s, $i + 1);
                $val = substr($s, 0, -1);
                $val = substr($val, 1, -1);
                return $val;
            } else if ($type == 'i') {
                return intval($len);
            } else if ($type == 'd') {
                return floatval($len);
            } else if ($type == 'a') {
                $s = substr($s, $i + 1);
                $val = $s;
                if ($val[strlen($val)-1] == ';') {
                    $val = substr($s, 0, -1);
                }
                $val = substr($val, 1, -1);

                $items = [];
                $start = 0;
                $end = 0;
                $scope = 0;
                while($end < strlen($val)) {
                    if ($val[$end] == '{') {
                        $scope += 1;
                    } else if ($val[$end] == '}') {
                        $scope -= 1;
                        if ($scope == 0) {
                            if ($start > 0) {
                                $start += 1;
                            }
                            $items[] = substr($val, $start, $end - $start + 1);
                            $start = $end;
                        }
                    } else if ($val[$end] == ';' && $scope == 0) {
                        if ($start > 0) {
                            $start += 1;
                        }
                        $items[] = substr($val, $start, $end - $start);
                        $start = $end;
                    }
                    $end += 1;
                }

                $even = true;
                $keys = [];
                $values = [];
                foreach ($items as $item) {
                    if ($item == '') {
                        continue;
                    }
                    $item .= ';';
                    if($even) {
                        $keys[] = $this->decodeCartObj($item);
                        $even = false;
                    } else {
                        $values[] = $this->decodeCartObj($item);
                        $even = true;
                    }
                }

                $arr = [];
                $cnt = min(count($keys), count($values));
                for ($i = 0; $i < $cnt; $i += 1) {
                    $arr[$keys[$i]] = $values[$i];
                }
                return $arr;
            } else {
                die("Invalid");
            }
        }

        return null;
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

        $carts = [];

        /*
        // TODO: DEBUG: Is this valid every time?
        $cookies = 'wordpress_test_cookie=WP%20Cookie%20check; wordpress_logged_in_ef80ef114711cf36b767a2aed961be0b=admin%7C1634743615%7CMbd4phDhWhZ8xYNXROvBmByoooxap90cKHX3gzTscQF%7Cf3dde3140d1a8b46d6e85381c68741574e901311cad5bed403e1547a8421539a; tk_ai=woo%3AFPmVmIAlkNb0%2B1BPYOMVtPoh; wp-settings-1=libraryContent%3Dbrowse%26ampposts_list_mode%3Dlist%26posts_list_mode%3Dlist%26editor%3Dtinymce%26hidetb%3D1%26post_dfw%3Don%26mfold%3Do; wp-settings-time-1=1634570817; woocommerce_items_in_cart=1; wp_woocommerce_session_ef80ef114711cf36b767a2aed961be0b=1%7C%7C1634743618%7C%7C1634740018%7C%7Cdce526d4f3bb9e6a547ab8d1aaf34927; woocommerce_cart_hash=ef8c6a3674526897001b81f6b8d14703';

        $url = 'https://homygadget.com/wp-json/autonami-admin/carts/recoverable?offset=0&limit=500&_locale=user';
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'x-wp-nonce: 1a3cbc90ca',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);

        $response = curl_exec($ch);

        curl_close($ch);

        if ($response != null && $response != '') {
            $response = json_decode($response, true);
            if (isset($response['result'])) {
                $carts = $response['result'];
            }
        }
        */

        $url = 'https://homygadget.com/zaroapi/carts.php';
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        if ($response != null && $response != '') {
            $carts = json_decode($response, true);
        }

        for ($cartIndex = 0; $cartIndex < count($carts); $cartIndex += 1) {
            $cart = $carts[$cartIndex];

            $carts[$cartIndex]['location'] = $this->decodeCartObj($cart['location']);
            $carts[$cartIndex]['cart_contents'] = $this->decodeCartObj($cart['cart_contents']);
            $carts[$cartIndex]['other_fields'] = $this->decodeCartObj($cart['other_fields']);
        }

        return response([
            'data' => $carts,
        ]);
    }
}
