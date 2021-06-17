<?php

/*
 * TODO
 * - What about errors? Leave as it is or throw exceptions?
 * - Types
 */

namespace Driverfury\Cloudflare;

class Cloudflare
{
    // TODO: Move this somewhere else?
    public static $nameservers = [
        'marek.ns.cloudflare.com',
        'naomi.ns.cloudflare.com',
    ];

    private const CLOUDFLARE_API_URL = 'https://api.cloudflare.com/client/v4/';

    private $apiKey = '';
    private $headers = [];

    private $errors = [];

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
        $this->headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];
    }

    private function addError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function getHeaders()
    {
        return $this->headers;
    }

    public function getZoneId(string $name)
    {
        $result = '';
        $zones = $this->getZones();
        foreach ($zones as $zone) {
            if ($zone['name'] == $name) {
                $result = $zone['id'];
                break;
            }
        }
        return $result;
    }

    public function getZones()
    {
        $url = self::CLOUDFLARE_API_URL . 'zones';
        $headers = $this->getHeaders();

        $thereAreOtherPages = true;
        $currentPage = 0;

        $error = false;
        $zones = [];
        while ($thereAreOtherPages) {
            $currentPage += 1;

            $params = [
                'per_page' => 50,
                'page' => $currentPage,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response, true);

            if (isset($response['success']) && $response['success']) {
                foreach ($response['result'] as $zone) {
                    $zones[] = $zone;
                }

                $thereAreOtherPages = (isset($response['result_info']) &&
                    isset($response['result_info']['total_pages']) &&
                    $response['result_info']['total_pages'] < $currentPage);
            } else {
                $error = true;
                $thereAreOtherPages = false;
                $this->addError('Cannot get zones list on Cloudflare - RESPONSE: ' . json_encode($response));
            }
        }

        if ($error) {
            return false;
        }
        return $zones;
    }

    public function createZone($name)
    {
        $toReturn = false;

        $url = self::CLOUDFLARE_API_URL . 'zones';
        $headers = $this->getHeaders();
        $params = [
            'name' => $name,
            'account' => [
                'id' => env('CLOUDFLARE_ACCOUNT_ID'),
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            $toReturn = $response['result'];
        } else {
            $this->addError('Cannot create zone ' . $name . ' on Cloudflare - RESPONSE: ' . json_encode($response));
        }

        return $toReturn;
    }

    public function deleteZone($id)
    {
        $toReturn = false;

        $url = self::CLOUDFLARE_API_URL . 'zones/' . $id;
        $headers = $this->getHeaders();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            $toReturn = true;
        }

        return $toReturn;
    }

    public function createDNSRecord(
        string $zone_id,
        string $type,
        string $name,
        string $content,
    )
    {
        $toReturn = false;

        $url = self::CLOUDFLARE_API_URL . 'zones/' . $zone_id . '/dns_records';
        $headers = $this->getHeaders();
        $params = [
            'type' => $type,
            'name' => $name,
            'content' => $content,
            'ttl' => 1,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            $toReturn = $response['result'];
        } else {
            $this->addError('Cannot create DNS record for zone ' . $zone_id . ' on Cloudflare - RESPONSE: ' . json_encode($response));
        }

        return $toReturn;
    }

    public function getDNSRecords($zone_id)
    {
        $url = self::CLOUDFLARE_API_URL . 'zones/' . $zone_id . '/dns_records';
        $headers = $this->getHeaders();

        $thereAreOtherPages = true;
        $currentPage = 0;

        $error = false;
        $dnsList = [];
        while ($thereAreOtherPages) {
            $currentPage += 1;

            $params = [
                'per_page' => 100,
                'page' => $currentPage,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response, true);

            if (isset($response['success']) && $response['success']) {
                foreach ($response['result'] as $dns) {
                    $dnsList[] = $dns;
                }

                $thereAreOtherPages = (isset($response['result_info']) &&
                    isset($response['result_info']['total_pages']) &&
                    $response['result_info']['total_pages'] < $currentPage);
            } else {
                $error = true;
                $thereAreOtherPages = false;
                $this->addError('Cannot get zone DNS list on Cloudflare - RESPONSE: ' . json_encode($response));
            }
        }

        if ($error) {
            return false;
        }
        return $dnsList;
    }

    public function addDNSRecord($zone_id, $type, $name, $value)
    {
        $toReturn = false;

        $url = self::CLOUDFLARE_API_URL . 'zones/' . $zone_id . '/dns_records';
        $headers = $this->getHeaders();
        $params = [
            'type' => $type,
            'name' => $name,
            'content' => $value,
            'ttl' => 1,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            $toReturn = $response['result'];
        } else {
            $this->addError('Cannot create DNS record on Cloudflare for the zone ' . $name . ' - RESPONSE: ' . json_encode($response));
        }

        return $toReturn;
    }

    public function deleteDNSRecord($zone_id, $dns_record_id)
    {
        $toReturn = false;

        $url = self::CLOUDFLARE_API_URL . 'zones/' . $zone_id . '/dns_records/' . $dns_record_id;
        $headers = $this->getHeaders();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            $toReturn = true;
        }

        return $toReturn;
    }
}
