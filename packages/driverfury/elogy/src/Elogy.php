<?php

namespace Driverfury\Elogy;

// DEBUG
use Illuminate\Support\Facades\Log;

class Elogy
{
    protected string $accessToken = '';
    protected string $refreshToken = '';
    protected static string $apiUrl = 'https://api.elogy.io:8443/api';
        
    public function __construct(string $apiKey, string $email, string $password)
    {
        $output = [];

        $headers = [
            'Authorization: ' . $apiKey,
            'Content-Type: application/json',
        ];

        $urlParams = [
            'email' => $email,
            'password' => $password,
            'source' => 'api',
            'host' => 'app.elogy.io',
        ];
        $bodyParams = $urlParams;

        $url  = 'https://api.elogy.io:8443/public-api/login';
        $url .= '?' . http_build_query($urlParams);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bodyParams));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch); 

        if ($response !== null) {
            $output = json_decode($response, true);
        }

        $this->accessToken = $output['token'];
        $this->refreshToken = $output['refresh_token'];
    }

    protected function getRequest(
        string $path,
        array $params = []): array
    {
        $output = [];

        if (strlen($path) === 0) {
            return $output;
        }
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        $headers = [
            'Authorization: ' . $this->accessToken,
            'Content-Type: application/json',
        ];

        foreach ($params as $key => $value) {
            $params[$key] = is_string($value) ? $value : json_encode($value);
        }

        $url = self::$apiUrl . $path . '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch); 

        if ($response !== null) {
            $output = json_decode($response, true);
        }

        return $output;
    }

    protected function postRequest(
        string $path,
        array $urlParams = [],
        array $bodyParams = []): array
    {
        $output = [];

        if (strlen($path) === 0) {
            return $output;
        }
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        $headers = [
            'Authorization: ' . $this->accessToken,
            'Content-Type: application/json',
        ];

        $url = self::$apiUrl . $path . '?' . http_build_query($urlParams);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bodyParams));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch); 

        if ($response !== null) {
            $output = json_decode($response, true);
        }

        return $output;
    }

    protected function putRequest(
        string $path,
        array $urlParams = [],
        array $bodyParams = []): array
    {
        $output = [];

        if (strlen($path) === 0) {
            return $output;
        }
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        $headers = [
            'Authorization: ' . $this->accessToken,
            'Content-Type: application/json',
        ];

        $url = self::$apiUrl . $path . '?' . http_build_query($urlParams);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bodyParams));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch); 

        if ($response !== null) {
            $output = json_decode($response, true);
        }

        return $output;
    }

    /*
    protected function refreshToken(): string
    {
        $output = [];

        if (strlen($path) === 0) {
            return $output;
        }
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        $headers = [
            'Authorization: ' . $this->accessToken,
            'Content-Type: application/json',
        ];

        $urlParams = [
            'refresh_token' => $this->accessToken,
            'user_id' =>          
        ];

        $url  = 'https://api.elogy.io:8443/public-api/refresh'
        $url .= '?' . http_build_query($urlParams);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bodyParams));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch); 

        if ($response !== null) {
            $output = json_decode($response, true);
        }

        return $output;
    }
    */

    protected function getRequestAllPages(string $path, array $params): array
    {
        $output = [];

        if(!isset($params['length'])) {
            $params['length'] = 100;
        }
        $params['offset'] = 0;
        $total = 0;
        $next = [];
        $endLoop = false;
        do {
            $response = $this->getRequest($path, $params);
            if ($response !== null && isset($response['data'])) {
                foreach ($response['data'] as $item) {
                    $output[] = $item;
                }
                $total += count($response['data']);
                if (isset($response['total']) &&
                    $total >= $response['total']) {
                    $endLoop = true;
                }
                $params['offset'] += count($response['data']);
            } else {
                $endLoop = true;
            }
        } while (!$endLoop);

        return $output;
    }

    public function getOrders(): array
    {
        $objects = [];

        $params = [
            'offset' => 0,
            'length' => 100,
        ];
        $objects = $this->getRequestAllPages('/orders', $params);

        return $objects;
    }

    public function getOrderByNumber(string $number): array|null
    {
        $object = null;

        $orders = $this->getOrders();
        foreach ($orders as $o) {
            if (strval($o['order_number']) === $number) {
                $object = $o;
                break;
            }
        }

        return $object;
    }

    public function getOrder(string $orderId): array|null
    {
        $object = null;

        $response = $this->getRequest('/orders/' . $orderId, []);
        if (isset($response['success']) && $response['success'] == true &&
            isset($response['data'])) {
            $object = $response['data'];
        }

        return $object;
    }

    public function createOrder(array $params): ?array
    {
        $response = $this->postRequest('/orders', $params, $params);
        if ($response !== null && isset($response['success'])) {
            if (isset($response['data']['failedOrders']) &&
                count($response['data']['failedOrders']) == 0) {
                return $response['data']['successOrders'][0];
            } else {
                Log::channel('elogy')->info('Error while pushing the order - RESPONSE: ' . json_encode($response));
                return null;
            }
        } else {
            Log::channel('elogy')->info('Error while pushing the order - RESPONSE: ' . json_encode($response));
            return null;
        }
        return null;
    }

    public function updateOrder(string $orderId, array $params): bool
    {
        $response = $this->putRequest('/orders/' . $orderId, $params, $params);
        if ($response !== null && isset($response['success']) && $response['success'] == true) {
            if (isset($params['customer'])) {
                $order = $this->getOrder($orderId);
                if ($order !== null && isset($order['customer_id']) && $order['customer_id'] !== null) {
                    $customerId = strval($order['customer_id']);
                    return $this->updateCustomer($customerId, $params['customer']);
                }
            }
            return true;
        }
        return false;
    }

    public function updateCustomer(string $customerId, array $params): bool
    {
        $response = $this->putRequest('/customers/' . $customerId, $params, $params);
        if ($response !== null && isset($response['success']) && $response['success'] == true) {
            return true;
        }
        return false;
    }

    public function getProducts(): array
    {
        $objects = [];

        $params = [
            'offset' => 0,
            'length' => 100,
        ];
        $objects = $this->getRequestAllPages('/products', $params);

        return $objects;
    }
}
