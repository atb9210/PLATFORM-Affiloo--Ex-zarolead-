<?php

namespace Driverfury\Worldfilia;

class Worldfilia
{
    protected string $apiToken = '';
    protected string $apiUrl = 'https://worldfilia.net/affiliateproject/rest';

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
        $params['token'] = $this->apiToken;
        $url = $this->apiUrl . $path . '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
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
        $urlParams['token'] = $this->apiToken;
        $url = $this->apiUrl . $path . '?' . http_build_query($urlParams);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch); 

        if ($response !== null) {
            $output = json_decode($response, true);
        }

        return $output;
    }

    public function __construct(string $apiToken)
    {
        $this->apiToken = $apiToken;
    }

    public function getCampaigns()
    {
        $campaigns = [];

        $maxRowCount = 200;
        $currentPage = 0;
        $lastPage = 0;
        do {
            $params = [
                'rowCount' => $maxRowCount,
                'current' => $currentPage,
            ];
            $output = $this->getRequest('/read/campaigns', $params);

            if (isset($output['current'])) {
                $currentPage = $output['current'];
            }
            if (isset($output['last_page'])) {
                $lastPage = $output['last_page'];
            }
            if (isset($output['rowCount'])) {
                $maxRowCount = $output['rowCount'];
            }

            if (isset($output['rows'])) {
                foreach ($output['rows'] as $row) {
                    $campaigns[] = $row;
                }
            }

            $currentPage += 1;
        } while ($currentPage <= $lastPage);

        return $campaigns;
    }

    public function getLandings(int $campaignId): array
    {
        $landings = [];

        $maxRowCount = 200;
        $currentPage = 0;
        $lastPage = 0;
        do {
            $params = [
                'rowCount' => $maxRowCount,
                'current' => $currentPage,
                'where[campaign_id]' => $campaignId,
            ];
            $output = $this->getRequest('/read/landings', $params);

            if (isset($output['current'])) {
                $currentPage = $output['current'];
            }
            if (isset($output['last_page'])) {
                $lastPage = $output['last_page'];
            }
            if (isset($output['rowCount'])) {
                $maxRowCount = $output['rowCount'];
            }

            if (isset($output['rows'])) {
                foreach ($output['rows'] as $row) {
                    $landings[] = $row;
                }
            }

            $currentPage += 1;
        } while ($currentPage <= $lastPage);

        return $landings;
    }
}
