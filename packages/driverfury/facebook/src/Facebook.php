<?php

namespace Driverfury\Facebook;

// DEBUG
use Illuminate\Support\Facades\Log;

class Facebook
{
    protected string $accessToken = '';
    protected static string $apiUrl = 'https://graph.facebook.com/v12.0';
        
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
            'Content-Type: application/json',
        ];

        if (!isset($params['access_token'])) {
            $params['access_token'] = $this->accessToken;
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
            $urlParams['access_token'] = $this->accessToken;
        }
        if (!isset($bodyParams['access_token'])) {
            $bodyParams['access_token'] = $this->accessToken;
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

        // NOTE: This improves performances
        if (!isset($params['limit'])) {
            $params['limit'] = 2000;
        }

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

    public function getMe(array $fields = []): array
    {
        $params = [];
        if (count($fields) > 0) {
            $params['fields'] = $fields;
        }
        $object = $this->getRequest('/me', $params);
        return $object;
    }

    public function getAdAccounts(
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
                $insights = 'insights';
                if (isset($params['time_range'])) {
                    $insights .= '.time_range(' . json_encode($params['time_range']) . ')';
                } else if (isset($params['date_preset'])) {
                    $insights .= '.date_preset(' . $params['date_preset'] . ')';
                }
                $insights .= '{';
                $insights .= implode(',', $reports['metrics']);
                $insights .= '}';
                $params['fields'][] = $insights;
            }
        }

        $objects = $this->getRequestAllPages('/me/adaccounts', $params);
        for ($i = 0; $i < count($objects); $i++) {
            if (isset($objects[$i]['insights']['data'][0])) {
                $insights = $objects[$i]['insights']['data'][0];
                unset($insights['date_start']);
                unset($insights['date_stop']);
                $objects[$i] = array_merge($objects[$i], $insights);
            }
        }

        return($objects);
    }

    public function getCampaigns(
        string $parentId,
        array $reports = []): array
    {
        $params = [
            'fields' => [
                'id',
                'name',
                'status',
                'effective_status',
                'objective',
                'daily_budget',
                'bid_strategy',
                'bid_amount',
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
                $insights = 'insights';
                if (isset($params['time_range'])) {
                    $insights .= '.time_range(' . json_encode($params['time_range']) . ')';
                } else if (isset($params['date_preset'])) {
                    $insights .= '.date_preset(' . $params['date_preset'] . ')';
                }
                $insights .= '{';
                $insights .= implode(',', $reports['metrics']);
                $insights .= '}';
                $params['fields'][] = $insights;
            }
        }

        $objects = $this->getRequestAllPages('/' . $parentId . '/campaigns', $params);
        for ($i = 0; $i < count($objects); $i++) {
            if (isset($objects[$i]['insights']['data'][0])) {
                $insights = $objects[$i]['insights']['data'][0];
                unset($insights['date_start']);
                unset($insights['date_stop']);
                $objects[$i] = array_merge($objects[$i], $insights);
            }
        }

        return($objects);
    }

    public function getAdsets(
        string $parentId,
        array $reports = []): array
    {
        $params = [
            'fields' => [
                'id',
                'campaign_id',
                'name',
                'status',
                'effective_status',
                'daily_budget',
                'bid_strategy',
                'bid_amount',
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
                $insights = 'insights';
                if (isset($params['time_range'])) {
                    $insights .= '.time_range(' . json_encode($params['time_range']) . ')';
                } else if (isset($params['date_preset'])) {
                    $insights .= '.date_preset(' . $params['date_preset'] . ')';
                }
                $insights .= '{';
                $insights .= implode(',', $reports['metrics']);
                $insights .= '}';
                $params['fields'][] = $insights;
            }
        }

        $objects = $this->getRequestAllPages('/' . $parentId . '/adsets', $params);
        for ($i = 0; $i < count($objects); $i++) {
            if (isset($objects[$i]['insights']['data'][0])) {
                $insights = $objects[$i]['insights']['data'][0];
                unset($insights['date_start']);
                unset($insights['date_stop']);
                $objects[$i] = array_merge($objects[$i], $insights);
            }
        }

        return($objects);
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
                $insights = 'insights';
                if (isset($params['time_range'])) {
                    $insights .= '.time_range(' . json_encode($params['time_range']) . ')';
                } else if (isset($params['date_preset'])) {
                    $insights .= '.date_preset(' . $params['date_preset'] . ')';
                }
                $insights .= '{';
                $insights .= implode(',', $reports['metrics']);
                $insights .= '}';
                $params['fields'][] = $insights;
            }
        }

        $objects = $this->getRequestAllPages('/' . $parentId . '/ads', $params);
        for ($i = 0; $i < count($objects); $i++) {
            if (isset($objects[$i]['insights']['data'][0])) {
                $insights = $objects[$i]['insights']['data'][0];
                unset($insights['date_start']);
                unset($insights['date_stop']);
                $objects[$i] = array_merge($objects[$i], $insights);
            }
        }

        return($objects);
    }

    public function updateStatus(
        string $objectId,
        string $status): bool
    {
        $params = [
          'status' => $status,
        ];
        $response = $this->postRequest('/' . $objectId, $params, $params);
        if (isset($response['error'])) {
            return false;
        }
        return true;
    }

    public function updateAdsetBudget(
        string $adsetId,
        float $budget): bool
    {
        $params = [
          'daily_budget' => intval($budget*100),
        ];
        $response = $this->postRequest('/' . $adsetId, $params, $params);
        if (isset($response['error'])) {
            return false;
        }
        return true;
    }

    public function updateAdsetBidAmount(
        string $adsetId,
        float $bidAmount): bool
    {
        $params = [
          'bid_amount' => intval($bidAmount*100),
        ];
        $response = $this->postRequest('/' . $adsetId, $params, $params);
        if (isset($response['error'])) {
            return false;
        }
        return true;
    }

    public function getLongLivedAccessToken(
        string $appId,
        string $appSecret): string
    {
        $newAccessToken = '';

        $params = [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'fb_exchange_token' => $this->accessToken,
        ];
        $response = $this->getRequest('/oauth/access_token', $params);
        if (isset($response['access_token'])) {
            $newAccessToken = $response['access_token'];
        }

        return $newAccessToken;
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

        // DEBUG
        Log::channel('facebook')->info('[OK] Event sent. Response: ' . json_encode($response));

        if (isset($response['events_received']) && $response['events_received'] > 0) {
            return true;
        }

        // DEBUG
        Log::channel('facebook')->info('[ER] Event not sent. Response: ' . json_encode($response));

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

        // DEBUG
        Log::channel('facebook')->info('[OK] Event sent. Response: ' . json_encode($response));

        if (isset($response['events_received']) && $response['events_received'] > 0) {
            return true;
        }

        // DEBUG
        Log::channel('facebook')->info('[ER] Event not sent. Response: ' . json_encode($response));

        return false;
    }
}
