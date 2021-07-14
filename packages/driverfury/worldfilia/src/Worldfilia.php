<?php

namespace Driverfury\Worldfilia;

class Worldfilia
{
    protected string $apiToken = '';
    protected string $apiUrl = 'https://worldfilia.net/affiliateproject/rest';

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

    public function getUserInfo()
    {
        $output = $this->getRequest('/me');
        return $output;
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

    public function getProductJson(
        int $campaignId,
        string $sourceId): string
    {
        $json = '';

        $landings = $this->getLandings($campaignId);
        foreach ($landings as $landing) {
            if ($landing['name'] === 'IFRAME') {
                $url = parse_url($landing['landing_preview_url']);
                $urlParams = [];
                parse_str($url['query'], $urlParams);
                if ($urlParams === NULL) {
                    $urlParams = [];
                }
                if (isset($urlParams['pub_id'])) {
                    $urlParams['pub_id'] = $sourceId;
                }
                if (isset($urlParams['ref'])) {
                    $urlParams['ref'] = $sourceId;
                }
                $url = $url['scheme'] . '://' . $url['host'] . $url['path'];
                $url = $url . '?' . http_build_query($urlParams);

                $sourceHTML = file_get_contents($url);

                $needle = ' action="';
                $needlePos = strpos($sourceHTML, $needle);
                if ($needlePos !== false) {
                    $tmp = substr($sourceHTML, $needlePos + strlen($needle));

                    $needle2 = '"';
                    $needlePos2 = strpos($tmp, $needle2);
                    if ($needlePos2 !== false) {
                        $formAction = substr($tmp, 0, $needlePos2);

                        $scheme = 'http';
                        if (substr($formAction, 0, strlen($scheme)) !== $scheme) {
                            if ($formAction != '' && $formAction[0] != '/') {
                                $formAction = '/' . $formAction;
                            }
                            $formAction =
                                'https://network.worldfilia.net' .
                                $formAction;

                            $formActionUrl = parse_url($formAction);

                            $json =
                                $formActionUrl['scheme'] . '://' .
                                $formActionUrl['host'] .
                                $formActionUrl['path'] .
                                '.json';
                            break;
                        }
                    }
                }
            }
        }

        return $json;
    }
}
