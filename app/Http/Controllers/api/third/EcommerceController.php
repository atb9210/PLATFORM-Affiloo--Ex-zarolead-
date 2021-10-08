<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Driverfury\Worldfilia\Worldfilia;

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

        $woo = $this->getClient();
        $objects = $woo->get('customers');

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
    public function orders()
    {
        // TODO: Authorization

        $woo = $this->getClient();
        $objects = $woo->get('orders');

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

        $woo = $this->getClient();
        $objects = $woo->get('products');

        return response([
            'data' => $objects,
        ]);
    }
}
