<?php

namespace Driverfury\FraudFilter;

class FraudFilter
{
    protected array $serverRequest = [];

    /**
     * Check fraud protection with FraudFilter.
     *
     * @param  string  $campaignId
     * @return bool Returns false if detected as fraud.
     */
    public function check(
        string $campaignId,
        string $key,
        array $serverRequest = []): bool
    {
        if (count($serverRequest) == 0) {
            $serverRequest = $_SERVER;
        }
        $this->serverRequest = $serverRequest;

        $url = 'http://130.211.20.155/' . $campaignId;

        // TODO: WTF is this?
        //$nParam = '339can';
        //if (isset($_GET[$nParam])) {
        //    $url = $url . '&' . $nParam . '=' . $_GET[$nParam];
        //}

        $ch = curl_init($url);

        $headers = $this->fillAllPostHeaders($key);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TCP_NODELAY, 1);

        $output = curl_exec($ch);
        $curl_error_number = curl_errno($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $output = trim($output);

        if ($output === '') {
            $this->notifyAboutError(
                'EMPTY_ANSWER_curl_error_number_' .
                    $curl_error_number .
                    '_output' .
                    $output .
                    '_http_status_' .
                    $http_status,
                $campaignId
            );

            return false;
        }

        if (strlen($output) <= 3) {
            $this->notifyAboutError(
                'SHORT_ANSWER_curl_error_number_' .
                    $curl_error_number .
                    '_output' .
                    $output .
                    '_http_status_' .
                    $http_status,
                $campaignId
            );

            return false;
        }

        $result = $output[0];
        $sep = $output[1];

        if (($result != '0' && $result != '1') || $sep != ';') {
            $this->notifyAboutError(
                'INVALID_PREFIX_curl_error_number_' .
                    $curl_error_number .
                    '_output' .
                    $output .
                    '_http_status_' .
                    $http_status,
                $campaignId
            );

            return false;
        }

        return $result === '1';
    }

    /**
     * Fill all required headers for FraudFilter request
     *
     * @return array
     */
    protected function fillAllPostHeaders(string $key): array
    {
        $headers = array();
        $headers[] = 'content-length: 0';
        $headers[] = 'X-FF-P: ' . $key;

        $this->addHeader($headers, 'X-FF-REMOTE-ADDR', 'REMOTE_ADDR');
        $this->addHeader(
            $headers,
            'X-FF-X-FORWARDED-FOR',
            'HTTP_X_FORWARDED_FOR'
        );
        $this->addHeader($headers, 'X-FF-X-REAL-IP', 'HTTP_X_REAL_IP');
        $this->addHeader(
            $headers,
            'X-FF-DEVICE-STOCK-UA',
            'HTTP_DEVICE_STOCK_UA'
        );
        $this->addHeader(
            $headers,
            'X-FF-X-OPERAMINI-PHONE-UA',
            'HTTP_X_OPERAMINI_PHONE_UA'
        );
        $this->addHeader($headers, 'X-FF-HEROKU-APP-DIR', 'HEROKU_APP_DIR');
        $this->addHeader($headers, 'X-FF-X-FB-HTTP-ENGINE', 'X_FB_HTTP_ENGINE');
        $this->addHeader($headers, 'X-FF-X-PURPOSE', 'X_PURPOSE');
        $this->addHeader($headers, 'X-FF-REQUEST-SCHEME', 'REQUEST_SCHEME');
        $this->addHeader(
            $headers,
            'X-FF-CONTEXT-DOCUMENT-ROOT',
            'CONTEXT_DOCUMENT_ROOT'
        );
        $this->addHeader($headers, 'X-FF-SCRIPT-FILENAME', 'SCRIPT_FILENAME');
        $this->addHeader($headers, 'X-FF-REQUEST-URI', 'REQUEST_URI');
        $this->addHeader($headers, 'X-FF-SCRIPT-NAME', 'SCRIPT_NAME');
        $this->addHeader($headers, 'X-FF-PHP-SELF', 'PHP_SELF');
        $this->addHeader(
            $headers,
            'X-FF-REQUEST-TIME-FLOAT',
            'REQUEST_TIME_FLOAT'
        );
        $this->addHeader($headers, 'X-FF-COOKIE', 'HTTP_COOKIE');
        $this->addHeader(
            $headers,
            'X-FF-ACCEPT-ENCODING',
            'HTTP_ACCEPT_ENCODING'
        );
        $this->addHeader(
            $headers,
            'X-FF-ACCEPT-LANGUAGE',
            'HTTP_ACCEPT_LANGUAGE'
        );
        $this->addHeader(
            $headers,
            'X-FF-CF-CONNECTING-IP',
            'HTTP_CF_CONNECTING_IP'
        );
        $this->addHeader(
            $headers,
            'X-FF-INCAP-CLIENT-IP',
            'HTTP_INCAP_CLIENT_IP'
        );
        $this->addHeader($headers, 'X-FF-QUERY-STRING', 'QUERY_STRING');
        $this->addHeader($headers, 'X-FF-X-FORWARDED-FOR', 'X_FORWARDED_FOR');
        $this->addHeader($headers, 'X-FF-ACCEPT', 'HTTP_ACCEPT');
        $this->addHeader($headers, 'X-FF-X-WAP-PROFILE', 'X_WAP_PROFILE');
        $this->addHeader($headers, 'X-FF-PROFILE', 'PROFILE');
        $this->addHeader($headers, 'X-FF-WAP-PROFILE', 'WAP_PROFILE');
        $this->addHeader($headers, 'X-FF-REFERER', 'HTTP_REFERER');
        $this->addHeader($headers, 'X-FF-HOST', 'HTTP_HOST');
        $this->addHeader($headers, 'X-FF-VIA', 'HTTP_VIA');
        $this->addHeader($headers, 'X-FF-CONNECTION', 'HTTP_CONNECTION');
        $this->addHeader(
            $headers,
            'X-FF-X-REQUESTED-WITH',
            'HTTP_X_REQUESTED_WITH'
        );
        $this->addHeader($headers, 'User-Agent', 'HTTP_USER_AGENT');
        $this->addHeader($headers, 'Expected', '');

        $hh = $this->getAllHeadersFF();
        $counter = 0;

        foreach ($hh as $key => $value) {
            $k = strtolower($key);
            if ($k === 'host') {
                $headers[] = 'X-FF-HOST-ORDER: ' . $counter;
                break;
            }
            $counter = $counter + 1;
        }

        return $headers;
    }

    protected function addHeader(&$headers, $out, $in)
    {
        if (!isset($this->serverRequest[$in])) {
            return;
        }

        $value = $this->serverRequest[$in];

        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $headers[] = $out . ': ' . $value;
    }

    protected function getAllHeadersFF()
    {
        $headers = [];

        foreach ($this->serverRequest as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[
                    str_replace(
                        ' ',
                        '-',
                        ucwords(
                            strtolower(str_replace('_', ' ', substr($name, 5)))
                        )
                    )
                ] = $value;
            }
        }

        return $headers;
    }

    protected function notifyAboutError($message, $campaignId)
    {
        $len = strlen($message);
        if ($len > 800) {
            $message = substr($message, 0, 800);
        }
        $message = urlencode($message);

        $url =
            'http://139.59.212.55/ff-notify.html?v=ff1&guid=' .
            $campaignId .
            '&m=' .
            $message;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);

        curl_exec($ch);
    }
}
