<?php

$response = file_get_contents('https://beta2.zarolead.com/api/v1/templates/{id}/get-strings');
$strings = json_decode($response, true);

include('template.php');

?>
