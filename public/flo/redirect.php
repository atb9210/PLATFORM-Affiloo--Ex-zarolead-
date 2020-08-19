<?php

$requestUrl = '';
$redirectUrl = '';

$prefix = 'http';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    $prefix .= 's';
}
$prefix .= '://';

$requestUrl = $_SERVER['HTTP_HOST'] . '/api/v1/flows/5/landings/get';

$curlSES = curl_init(); 
curl_setopt($curlSES, CURLOPT_URL, $requestUrl);
curl_setopt($curlSES, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curlSES, CURLOPT_HEADER, false); 
$response = curl_exec($curlSES);
$httpcode = curl_getinfo($curlSES, CURLINFO_HTTP_CODE);
curl_close($curlSES);

if ($httpcode == 200) {
    $responseObj = json_decode($response, TRUE);
    $redirectUrl = $responseObj['data'];
}

header('Location: ' . $redirectUrl);
