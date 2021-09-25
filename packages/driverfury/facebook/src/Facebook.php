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

    protected function getRequestAllPages(string $path, array $params): array
    {
        $output = [];

        $next = [];
        $endLoop = false;
        do {
            $response = $this->getRequest($path, $params);
            if ($response !== null && isset($response['data'])) {
                foreach ($response['data'] as $item) {
                    $output[] = $item;
                }
            }

            if (isset($response['paging']['next'])) {
                $tmp = parse_url($response['paging']['next']);
                parse_str($tmp['query'], $next);
                $params = array_merge($params, $next);
            } else {
                $endLoop = true;
            }
        } while (!$endLoop);

        return $output;
    }

    public function getCampaigns(
        string $advertiserId,
        array $reports = []): array
    {
        $params = [
            'fields' => [
                'id',
                'name',
                'daily_budget',
            ],
            'date_preset' => 'maximum',
        ];

        if (count($reports) > 0) {
            if (isset($reports['start_date'])) {
                $params['time_range']['since'] = $reports['start_date'];
                unset($params['date_preset']);
            }
            if (isset($reports['end_date'])) {
                $params['time_range']['until'] = $reports['end_date'];
                unset($params['date_preset']);
            }
            if (isset($reports['metrics'])) {
                // TODO
            }
        }

        $data = $this->getRequestAllPages('/act_' . $advertiserId . '/campaigns', $params);
        return($data);
    }

    public function getAdsets(
        string $parentId,
        array $reports = []): array
    {
        $params = [
            'fields' => [
                'id',
                'name',
            ],
            'date_preset' => 'maximum',
        ];

        if (count($reports) > 0) {
            if (isset($reports['start_date'])) {
                $params['time_range']['since'] = $reports['start_date'];
                unset($params['date_preset']);
            }
            if (isset($reports['end_date'])) {
                $params['time_range']['until'] = $reports['end_date'];
                unset($params['date_preset']);
            }
            if (isset($reports['metrics'])) {
                // TODO
            }
        }

        $data = $this->getRequestAllPages('/' . $parentId . '/adsets', $params);
        return($data);
    }

    public function getAds(
        string $parentId,
        array $reports = []): array
    {
        $params = [
            'fields' => [
                'id',
                'name',
            ],
            'date_preset' => 'maximum',
        ];

        if (count($reports) > 0) {
            if (isset($reports['start_date'])) {
                $params['time_range']['since'] = $reports['start_date'];
                unset($params['date_preset']);
            }
            if (isset($reports['end_date'])) {
                $params['time_range']['until'] = $reports['end_date'];
                unset($params['date_preset']);
            }
            if (isset($reports['metrics'])) {
                // TODO
            }
        }

        $data = $this->getRequestAllPages('/' . $parentId . '/ads', $params);
        return($data);
    }

    public function sendEvent(
        string $pixel,
        string $event,
        array $userData,
        string $eventSourceUrl,
        array $customData = []): bool
    {
        $params = [
            'data' => [
                [
                    'event_name' => $event,
                    'event_time' => strtotime('now'),
                    'user_data' => $userData,
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
        array $userData,
        string $eventSourceUrl,
        array $customData = []): bool
    {
        $params = [
            'test_event_code' => $testCode,
            'data' => [
                [
                    'event_name' => $event,
                    'event_time' => strtotime('now'),
                    'user_data' => $userData,
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
