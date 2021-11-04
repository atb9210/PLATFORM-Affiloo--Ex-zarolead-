<?php

namespace Driverfury\Elogy;

// DEBUG
use Illuminate\Support\Facades\Log;

class Elogy
{
    protected string $accessToken = '';
    protected static string $apiUrl = 'https://api.elogy.io:8443/api';
        
    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
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
        $orders = [];

        $params = [
            'offset' => 0,
            'length' => 100,
        ];
        $orders = $this->getRequestAllPages('/orders', $params);

        return $orders;
    }

    public function createOrder(array $params): bool
    {
        $response = $this->postRequest('/orders', $params, $params);
        if ($response !== null && isset($response['success'])) {
            if (isset($response['data']['failedOrders']) &&
                count($response['data']['failedOrders']) == 0) {
                return true;
            } else {
                Log::channel('elogy')->info('Error while pushing the order - RESPONSE: ' . json_encode($response));
                return false;
            }
        }
        return false;
    }
}
