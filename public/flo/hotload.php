<?php

/*
//$flow_id = {{flow_id}};
$flow_id = 1;

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
*/

/*
$redirectUrl = 'http://127.0.0.1:8000/flo/test.php';
?>
<body style="margin:0px;padding:0px;overflow:hidden">
    <iframe src="<?php echo $redirectUrl; ?>" frameborder="0" style="overflow:hidden;height:100%;width:100%" height="100%" width="100%"></iframe>
</body>
*/
require_once("http://google.com");
