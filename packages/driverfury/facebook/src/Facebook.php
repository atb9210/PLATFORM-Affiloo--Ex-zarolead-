<?php

namespace Driverfury\Facebook;

// DEBUG
use Illuminate\Support\Facades\Log;

class Facebook
{
    protected string $apiToken = '';
    protected static string $apiUrl = 'https://graph.facebook.com/v12.0';
        
    public function __construct(string $apiToken)
    {
        $this->apiToken = $apiToken;
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
            'Content-Type: application/json',
        ];

        if (!isset($params['access_token'])) {
            $params['access_token'] = $this->apiToken;
        }

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
            'Content-Type: application/json',
        ];

        if (!isset($urlParams['access_token'])) {
            $urlParams['access_token'] = $this->apiToken;
        }
        if (!isset($bodyParams['access_token'])) {
            $bodyParams['access_token'] = $this->apiToken;
        }

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

    public function sendEvent(
        string $pixel,
        string $event,
        string $clickId,
        string $eventSourceUrl,
        array $customData = []): bool
    {
        $params = [
            'data' => [
                [
                    'event_name' => $event,
                    'event_time' => strtotime('now'),
                    'user_data' => [
                        'fbc' => $clickId,
                    ],
                    'event_source_url' => $eventSourceUrl,
                    'action_source' => 'website',
                    'custom_data' => $customData,
                ],
            ],
        ];

        $response = $this->postRequest('/' . $pixel . '/events', $params, $params);
        if (isset($response['events_received']) && $response['events_received'] > 0) {
            return true;
        }

        // DEBUG
        Log::channel('facebook')->info('Event not sent. Response: ' . json_encode($response));

        return false;
    }

    public function sendEventTest(
        string $pixel,
        string $testCode,
        string $event,
        string $clickId,
        string $eventSourceUrl,
        array $customData = []): bool
    {
        $params = [
            'test_event_code' => $testCode,
            'data' => [
                [
                    'event_name' => $event,
                    'event_time' => strtotime('now'),
                    'user_data' => [
                        'fbc' => $clickId,
                    ],
                    'event_source_url' => $eventSourceUrl,
                    'action_source' => 'website',
                    'custom_data' => $customData,
                ],
            ],
        ];

        $response = $this->postRequest('/' . $pixel . '/events', $params, $params);
        if (isset($response['events_received']) && $response['events_received'] > 0) {
            return true;
        }

        // DEBUG
        Log::channel('facebook')->info('Event not sent. Response: ' . json_encode($response));

        return false;
    }
}
