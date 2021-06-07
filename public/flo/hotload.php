<?php

$ch = curl_init();
$api_url = '[[APP_URL]]/api/v1/flows/[[FLOW_ID]]/hotload';
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);
$response = json_decode($response, true);
echo $response['html'];
