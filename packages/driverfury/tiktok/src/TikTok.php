<?php

namespace Driverfury\TikTok;

class TikTok
{
    protected string $apiToken = '';
    protected static string $apiUrl = 'https://ads.tiktok.com/open_api/v1.2';
        
    public function __construct(string $apiToken)
    {
        $this->apiToken = $apiToken;
    }

    public static function generateAccessToken(
        string $appId,
        string $secret,
        string $authCode): string
    {
        $ch = curl_init();

        $params = [
            'app_id' => $appId,
            'secret' => $secret,
            'auth_code' => $authCode,
        ];
        $url = self::$apiUrl . '/oauth2/access_token/';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close ($ch);

        $accessToken = '';
        $response = json_decode($response, true);
        if ($response['code'] == 0) {
            $accessToken = $response['data']['access_token'];
        }

        return $accessToken;
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
            'Access-Token: ' . $this->apiToken,
        ];

        foreach ($params as $key => $value) {
            $params[$key] = is_string($value) ? $value : json_encode($value);
        }

        $url = self::$apiUrl . $path . '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // NOTE: I commented this because it will automatically make your request
        // a POST request (or TikTok will consider it as a POST req instead of
        // a GET req).
        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
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
            'Access-Token: ' . $this->apiToken,
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

        $totalPages = 2;
        $currentPage = 1;
        while($currentPage < $totalPages) {
            $params['page_size'] = 1000;
            $params['page'] = $currentPage;
            $response = $this->getRequest($path, $params);
            if (isset($response['code']) && $response['code'] != 0) {
                $totalPages = 0;
                continue;
            }
            $currentPage = $response['data']['page_info']['page'];
            $totalPages = $response['data']['page_info']['total_page'];
            foreach ($response['data']['list'] as $element) {
                $output[] = $element;
            }
        }

        return $output;
    }

    private function getObjectIndexByField(
        array $objects,
        string $field,
        string $value)
    {
        for ($i = 0; $i < count($objects); $i++) {
            $obj = $objects[$i];
            if (isset($obj[$field]) && $obj[$field] == $value) {
                return $i;
            }
        }
        return -1;
    }

    public function getCampaigns(
        string $advertiserId,
        array $reports = []): array
    {
        $params = [
            'advertiser_id' => $advertiserId,
        ];
        $objects = $this->getRequestAllPages('/campaign/get/', $params);

        if (count($reports) > 0 &&
            isset($reports['metrics']) &&
            count($reports['metrics']) > 0) {
            $metrics = $reports['metrics'];
            $reportsParams = [
                'advertiser_id' => $advertiserId,
                'report_type' => 'BASIC',
                'data_level' => 'AUCTION_CAMPAIGN',
                'dimensions' => json_encode(['campaign_id']),
                'metrics' => $metrics,
                'lifetime' => true,
            ];
            if (isset($reports['start_date'])) {
                $reportsParams['start_date'] = $reports['start_date'];
                $reportsParams['lifetime'] = false;
            }
            if (isset($reports['end_date'])) {
                $reportsParams['end_date'] = $reports['end_date'];
                $reportsParams['lifetime'] = false;
            }

            $reports = $this->getRequestAllPages(
                '/reports/integrated/get/',
                $reportsParams);

            foreach ($reports as $report) {
                if (isset($report['dimensions']['campaign_id'])) {
                    $index = $this->getObjectIndexByField($objects, 'campaign_id', $report['dimensions']['campaign_id']);
                    if ($index >= 0 && isset($report['metrics'])) {
                        $objects[$index]['reports'] = [
                            'metrics' => $report['metrics'],
                        ];
                    }
                }
            }
        }

        return($objects);
    }

    public function getAdgroups(
        string $advertiserId,
        string $parentId = '',
        array $reports = []): array
    {
        $params = [
            'advertiser_id' => $advertiserId,
        ];
        if ($parentId !== '') {
            $params['filtering'] = [
                'campaign_ids' => [
                    $parentId,
                ],
            ];
        }
        $objects = $this->getRequestAllPages('/adgroup/get/', $params);

        if (count($reports) > 0 &&
            isset($reports['metrics']) &&
            count($reports['metrics']) > 0) {
            $metrics = $reports['metrics'];
            $reportsParams = [
                'advertiser_id' => $advertiserId,
                'report_type' => 'BASIC',
                'data_level' => 'AUCTION_ADGROUP',
                'dimensions' => json_encode(['adgroup_id']),
                'metrics' => $metrics,
                'lifetime' => true,
            ];
            if (isset($reports['start_date'])) {
                $reportsParams['start_date'] = $reports['start_date'];
                $reportsParams['lifetime'] = false;
            }
            if (isset($reports['end_date'])) {
                $reportsParams['end_date'] = $reports['end_date'];
                $reportsParams['lifetime'] = false;
            }

            $reports = $this->getRequestAllPages(
                '/reports/integrated/get/',
                $reportsParams);
            foreach ($reports as $report) {
                if (isset($report['dimensions']['adgroup_id'])) {
                    $index = $this->getObjectIndexByField($objects, 'adgroup_id', $report['dimensions']['adgroup_id']);
                    if ($index >= 0 && isset($report['metrics'])) {
                        $objects[$index]['reports'] = [
                            'metrics' => $report['metrics'],
                        ];
                    }
                }
            }
        }

        return($objects);
    }

    public function getAds(
        string $advertiserId,
        string $parentId = '',
        array $reports = []): array
    {
        $params = [
            'advertiser_id' => $advertiserId,
        ];
        if ($parentId !== '') {
            $params['filtering'] = [
                'adgroup_ids' => [
                    $parentId,
                ],
            ];
        }
        $objects = $this->getRequestAllPages('/ad/get/', $params);

        if (count($reports) > 0 &&
            isset($reports['metrics']) &&
            count($reports['metrics']) > 0) {
            $metrics = $reports['metrics'];
            $reportsParams = [
                'advertiser_id' => $advertiserId,
                'report_type' => 'BASIC',
                'data_level' => 'AUCTION_AD',
                'dimensions' => json_encode(['ad_id']),
                'metrics' => $metrics,
                'lifetime' => true,
            ];
            if (isset($reports['start_date'])) {
                $reportsParams['start_date'] = $reports['start_date'];
                $reportsParams['lifetime'] = false;
            }
            if (isset($reports['end_date'])) {
                $reportsParams['end_date'] = $reports['end_date'];
                $reportsParams['lifetime'] = false;
            }

            $reports = $this->getRequestAllPages(
                '/reports/integrated/get/',
                $reportsParams);
            foreach ($reports as $report) {
                if (isset($report['dimensions']['ad_id'])) {
                    $index = $this->getObjectIndexByField($objects, 'ad_id', $report['dimensions']['ad_id']);
                    if ($index >= 0 && isset($report['metrics'])) {
                        $objects[$index]['reports'] = [
                            'metrics' => $report['metrics'],
                        ];
                    }
                }
            }
        }

        return($objects);
    }

    public function updateCampaignStatus(
        string $advertiserId,
        string $campaignId,
        string $status): bool
    {
        $params = [
            'advertiser_id' => $advertiserId,
            'campaign_ids' => [intval($campaignId)],
            'opt_status' => $status,
        ];
        $output = $this->postRequest('/campaign/update/status/', $params, $params);
        if (isset($output['code']) && $output['code'] == 0)
        {
            return true;
        }
        return false;
    }

    public function updateAdgroupStatus(
        string $advertiserId,
        string $adgroupId,
        string $status): bool
    {
        $params = [
            'advertiser_id' => $advertiserId,
            'adgroup_ids' => [intval($adgroupId)],
            'status' => $status,
            'opt_status' => $status,
        ];
        $output = $this->postRequest('/adgroup/update/status/', $params, $params);
        if (isset($output['code']) && $output['code'] == 0)
        {
            return true;
        }
        return false;
    }

    public function updateAdStatus(
        string $advertiserId,
        string $adId,
        string $status): bool
    {
        $params = [
            'advertiser_id' => $advertiserId,
            'ad_ids' => [intval($adId)],
            'opt_status' => $status,
        ];
        $output = $this->postRequest('/ad/update/status/', $params, $params);
        if (isset($output['code']) && $output['code'] == 0)
        {
            return true;
        }
        return false;
    }

    public function updateAdgroupBudget(
        string $advertiserId,
        string $adgroupId,
        float  $budget): bool
    {
        $params = [
            'advertiser_id' => $advertiserId,
            'budget' => [
                [
                    'budget' => strval($budget),
                    'adgroup_id' => intval($adgroupId),
                ],
            ],
        ];
        $output = $this->postRequest('/adgroup/update/budget/', $params, $params);
        if (isset($output['code']) && $output['code'] == 0)
        {
            return true;
        }
        return false;
    }

    public function updateAdgroupBid(
        string $advertiserId,
        string $adgroupId,
        string $bidStrategy,
        float  $bid): bool
    {
        $params = [
            'advertiser_id' => $advertiserId,
            'adgroup_id' => $adgroupId,
            'bid_strategy' => $bidStrategy,
            'conversion_bid' => strval($bid),
        ];
        $output = $this->postRequest('/adgroup/update/', $params, $params);
        if (isset($output['code']) && $output['code'] == 0)
        {
            return true;
        }
        return false;
    }

    public function getImageInfo(
        string $advertiserId,
        string $imageId): array
    {
        $params = [
            'advertiser_id' => $advertiserId,
            'image_ids' => [$imageId],
        ];
        $output = $this->getRequest('/file/image/ad/info/', $params);

        if (isset($output['code']) && $output['code'] == 0) {
            $output = $output['data']['list'][0];
        }

        return $output;
    }

    public function triggerEvent(
        string $pixel,
        string $event,
        string $ttclid = '',
        string $properties = [])
    {
        $params = [
            'pixel_code' => $pixel,
            'event' => $event,
            'properties' => $properties,
        ];
        if ($ttclid != '') {
            $params['context'] = [
                'ad' => [
                    'callback' => $ttclid,
                ],
            ];
        }
        $output = $this->postRequest('/pixel/track/', $params, $params);
        if (isset($output['code']) && $output['code'] == 0)
        {
            return true;
        }
        return false;
    }
}
