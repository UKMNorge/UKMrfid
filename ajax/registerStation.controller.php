<?php

// Station er definert i api.php
global $station;
$result = $station->registerNew($_POST['guid']);

if ($result) {
	$JSON->success = true;
	$JSON->message = "Stasjon registrert";
} else {
	$JSON->success = false;
	$JSON->message = "Klarte ikke å registrere stasjonen - finnes den allerede?";
}

?>