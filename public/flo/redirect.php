<?php

$flow_id = {{flow_id}};

$requestUrl = '';
$redirectUrl = '';

$prefix = 'http';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    $prefix .= 's';
}
$prefix .= '://';

$requestUrl = $prefix . $_SERVER['HTTP_HOST'] . '/api/v1/flows/' . $flow_id . '/landings/get';

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

$separator = '&';
if (strpos($redirectUrl, '?') === false) {
    $separator = '?';
}
$redirectUrl .= $separator . http_build_query($_GET);

echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $redirectUrl . '">';
echo "<script type='text/javascript'>document.location.href='{$redirectUrl}';</script>";
