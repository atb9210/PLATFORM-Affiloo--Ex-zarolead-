<?php

namespace Driverfury\Dropin;

use Driverfury\Requests\Requests;

// DEBUG
use Illuminate\Support\Facades\Log;

class Dropin
{
    protected static string $apiUrl = 'https://dropship.eurowholesale.eu';
    protected static string $apiUrlProducts = 'https://eurowholesale.eu';
    protected Requests|null $http = null;

    protected string $email = '';
    protected string $password = '';
    protected bool $loggedIn = false;

    public function __construct(string $email = '', string $password = '')
    {
        $this->http = new Requests();

        $this->email = $email;
        $this->password = $password;
        $this->loggedIn = false;
        if ($this->email != '' && $this->password != '') {
            $this->loggedIn = $this->login($this->email, $this->password);
        }
    }

    protected function login(string $email, string $password)
    {
        $headers = [
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ];
        $response = $this->http->get(self::$apiUrl . '/sanctum/csrf-cookie', $headers);
        if ($response['code'] != 204) {
            return false;
        }

        $cookies = $this->http->getCookies();
        if (isset($cookies['XSRF-TOKEN'])) {
            $xsrfToken = $cookies['XSRF-TOKEN'];
            $headers['x-xsrf-token'] = $xsrfToken;
        }
        $headers['referer'] = 'https://dropship.eurowholesale.eu/auth/login';
        $headers['user-agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36';
        $headers['x-requested-with'] = 'XMLHttpRequest';

        $response = $this->http->post(self::$apiUrl . '/login', $headers, [
            'email' => $email,
            'password' => $password,
            'status' => true,
        ]);
        if ($response['code'] != 204) {
            return false;
        }

        return true;
    }

    protected function getHeaders()
    {
        $headers = [
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'referer' => 'https://dropship.eurowholesale.eu/orders-list',
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36',
            'x-requested-with' => 'XMLHttpRequest',
        ];

        $cookies = $this->http->getCookies();
        if (isset($cookies['XSRF-TOKEN'])) {
            $xsrfToken = $cookies['XSRF-TOKEN'];
            $headers['x-xsrf-token'] = $xsrfToken;
        }

        return $headers;
    }

    public function getOrders(): array
    {
        //TODO: What if he's not logged in?

        $objects = [];
        $response = $this->http->get(self::$apiUrl . '/api/orders-list', $this->getHeaders());
        if ($response['code'] == 200) {
            $objects = $response['data'];
        }
        return $objects;
    }

    public function getOrderDetails($id): array|null
    {
        // TODO: What if he's not logged in?

        $object = null;
        $response = $this->http->get(self::$apiUrl . '/api/order-details', $this->getHeaders(), ['orderId' => $id]);
        if ($response['code'] == 200) {
            $object = $response['data'];
        }
        return $object;
    }

    protected function getRequestXML(string $path): array
    {
        $output = [];

        if (strlen($path) === 0) {
            return $output;
        }
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        $content = file_get_contents($url);

        if ($content !== null) {
            try {
                $obj = simplexml_load_string($content);
                $output = json_decode(json_encode($obj), true);
            } catch (Exception $e) {
                $output = [];
            }
        }

        return $output;
    }

    public function getProducts(): array
    {
        $objects = $this->getRequestXML(self::$apiUrlProducts . '/partner_exports/it/it-zaro/65/it-zaro-620e4273c1c2d.xml');

        $objects = array_map(function ($objs) {
            $mappedObjects = [];
            foreach ($objs as $obj) {
                $description = '';
                if (!is_array($obj['Description'])) {
                    $description = $obj['Description'];
                }
                $variations = '';
                if (!is_array($obj['Variation'])) {
                    $variations = $obj['Variation'];
                }
                $images = [];
                if (isset($obj['Product_Images']['Image'])) {
                    $images = $obj['Product_Images']['Image'];
                }
                $mappedObjects[] = [
                    'name' => $obj['Product_Name'],
                    'description' => $description,
                    'category' => $obj['Category'],
                    'sku' => $obj['SKU'],
                    'variations' => $variations,
                    'dropshipping_price' => $obj['Dropshipping_Price'],
                    'sale_price' => $obj['Suggested_Sale_Price'],
                    'price' => $obj['Suggested_Sale_Price'],
                    'quantity' => $obj['Total_Quantity'],
                    'images' => $images,
                ];
            }
            return $mappedObjects;
        }, $objects);
        $objects = $objects['product'];

        return $objects;
    }
}
