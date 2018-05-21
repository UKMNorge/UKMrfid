<?php

// Stations er definert i api.php
$result = $stations->registerNew($_POST['guid']);

if ($result) {
	$JSON->success = true;
	$JSON->message = "Stasjon registrert";
} else {
	$JSON->success = false;
	$JSON->message = "Klarte ikke å registrere stasjonen - finnes den allerede?";
}

?>