<?php

namespace Driverfury\Requests;

class Requests
{
    protected $cookies = [];

    protected function headerKeysLowercase(array|null $headers): array|null
    {
        if ($headers === null) {
            return null;
        }

        $newHeaders = [];
        foreach ($headers as $k => $v) {
            $lowerKey = strtolower(trim($k));
            $newHeaders[$lowerKey] = $v;
        }

        return $newHeaders;
    }

    protected function headerFormatToArray(array $lineHeaders): array
    {
        $headers = [];
        foreach ($lineHeaders as $line) {
            $pos = strpos($line, ':');
            if ($line == '' || $pos === false) {
                continue;
            }
            $k = strtolower(trim(substr($line, 0, $pos)));
            $v = trim(substr($line, $pos + 1));
            if (!isset($headers[$k])) {
                $headers[$k] = $v;
            } else {
                $headers[$k] .= ', ' . $v;
            }
        }
        return $headers;
    }

    protected function arrayToHeaderFormat(array $headers): array
    {
        $lineHeaders = [];
        foreach ($headers as $k => $v) {
            $lineHeaders[] = $k . ': ' . $v;
        }
        return $lineHeaders;
    }

    protected function getFullHeaders(array|null $headers): array
    {
        $newHeaders = [];

        if ($headers !== null) {
            foreach ($headers as $k => $v) {
                $newHeaders[$k] = $v;
            }
        }

        foreach ($this->cookies as $k => $v) {
            if (!isset($newHeaders['cookie'])) {
                $newHeaders['cookie'] = '';
            }
            $newHeaders['cookie'] .= $k . '=' . $v .'; ';
        }

        $newHeaders = $this->arrayToHeaderFormat($newHeaders);

        return $newHeaders;
    }

    protected function parseHeaders(string $strHeaders): array
    {
        $strHeaders = str_replace("\r\n\r\n", "", $strHeaders);
        $lineHeaders = explode("\r\n", $strHeaders);
        // Remove the first line of the header (Example: HTTP/1.1 200)
        if (count($lineHeaders) > 0) {
            array_shift($lineHeaders);
        }
        return $this->headerFormatToArray($lineHeaders);
    }

    public function get(string $url, array|null $headers = [], array|null $params = []): array
    {
        $headers = $this->headerKeysLowercase($headers);

        if ($params !== null && count($params) > 0) {
            if (strpos($url, '?') === false) {
                $url .= '?';
            } else {
                $url .= '&';
            }
            $url .= http_build_query($params);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getFullHeaders($headers));
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $headersContent = substr($response, 0, $header_size);
        $data = substr($response, $header_size);

        $headers = $this->parseHeaders($headersContent);
        $cookies = $this->getCookiesFromResponseHeader($headersContent);
        $this->addCookies($cookies);

        if (isset($headers['content-type'])) {
            $cType = $headers['content-type'];
            if (strpos($cType, 'application/json') !== false) {
                $data = json_decode($data, true);
            }
        }

        return [
            'code' => $code,
            'headers' => $headers,
            'data' => $data,
            'cookies' => $this->cookies,
        ];
    }

    public function post(string $url, array|null $headers = [], array|null $params = []): array
    {
        $headers = $this->headerKeysLowercase($headers);

        $cType = '';
        if (isset($headers['content-type'])) {
            $cType = $headers['content-type'];
        }

        if (strpos($cType, 'application/json') !== false) {
            $params = json_encode($params);
        } else {
            $params = http_build_query($params);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getFullHeaders($headers));
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $headersContent = substr($response, 0, $header_size);
        $data = substr($response, $header_size);

        $headers = $this->parseHeaders($headersContent);
        $cookies = $this->getCookiesFromResponseHeader($headersContent);
        $this->addCookies($cookies);

        if (isset($headers['content-type'])) {
            $cType = $headers['content-type'];
            if (strpos($cType, 'application/json') !== false) {
                $data = json_decode($data, true);
            }
        }

        return [
            'code' => $code,
            'headers' => $headers,
            'data' => $data,
            'cookies' => $this->cookies,
        ];
    }

    protected function getCookiesFromResponseHeader(string $strHeaders): array
    {
        $cookies = [];

        $strHeaders = str_replace("\r\n\r\n", "", $strHeaders);
        $lineHeaders = explode("\r\n", $strHeaders);
        // Remove the first line of the header (Example: HTTP/1.1 200)
        if (count($lineHeaders) > 0) {
            array_shift($lineHeaders);
        }

        $lineCookies = [];
        foreach ($lineHeaders as $line) {
            $pos = strpos($line, ':');
            if ($line == '' || $pos === false) {
                continue;
            }
            $k = strtolower(trim(substr($line, 0, $pos)));
            $v = rawurldecode(trim(substr($line, $pos + 1)));
            if ($k === 'set-cookie') {
                $lineCookies[] = $v;
            }
        }

        foreach ($lineCookies as $line) {
            $tmp = explode(';', $line);
            if (count($tmp) < 1) {
                continue;
            }
            $kv = $tmp[0];
            $kv = explode('=', $kv);
            if (count($kv) < 2) {
                continue;
            }
            $k = trim($kv[0]);
            $v = trim($kv[1]);
            $cookies[$k] = $v;
        }

        return $cookies;
    }

    protected function addCookies(array $cookies): array
    {
        foreach ($cookies as $k => $v) {
            $this->cookies[$k] = $v;
        }
        return $this->cookies;
    }

    public function getCookies(): array
    {
        $cookies = [];
        foreach ($this->cookies as $k => $v) {
            $cookies[$k] = $v;
        }
        return $cookies;
    }

    public function setCookie(string $key, string $value)
    {
        if ($key == '') {
            return;
        }
        $this->cookies[$key] = $value;
    }

    public function postFileText(string $url, string $fileName, string $fileContents, array|null $headers = []): array
    {
        $headers = $this->headerKeysLowercase($headers);

        $cType = '';
        if (isset($headers['content-type'])) {
            $cType = $headers['content-type'];
        }

        // TODO: Support other MIME types
        $extToMIME = [
            'txt' => 'text/plain',
            'html' => 'text/html',
            'htm' => 'text/html',
            'csv' => 'text/csv',
        ];
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        if ($fileExt === null) {
            $fileExt = '';
        }
        $mime = 'text/plain';
        if (array_key_exists($fileExt, $extToMIME)) {
            $mime = $extToMIME[$fileExt];
        }

        $fileContents = 'data://application/octet-stream;base64,' . base64_encode($fileContents);
        $curlFile = new \CURLFile($fileContents, $mime, $fileName);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => $curlFile]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getFullHeaders($headers));
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $headersContent = substr($response, 0, $header_size);
        $data = substr($response, $header_size);

        $headers = $this->parseHeaders($headersContent);
        $cookies = $this->getCookiesFromResponseHeader($headersContent);
        $this->addCookies($cookies);

        if (isset($headers['content-type'])) {
            $cType = $headers['content-type'];
            if (strpos($cType, 'application/json') !== false) {
                $data = json_decode($data, true);
            }
        }

        return [
            'code' => $code,
            'headers' => $headers,
            'data' => $data,
            'cookies' => $this->cookies,
        ];
    }
}
