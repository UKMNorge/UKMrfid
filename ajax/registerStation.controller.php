<?php

// Station er definert i api.php
$scanner = \UKMNorge\RFID\Scanner::create($_POST['guid'], $_SERVER['REMOTE_ADDR']);

if ( is_object($scanner) ) {
	$JSON->success = true;
	$JSON->message = "Stasjon registrert";
} else {
	$JSON->success = false;
	$JSON->message = "Klarte ikke å registrere stasjonen - finnes den allerede?";
	$JSON->message .= "GUID: " . var_export($_POST['guid'], true);
	$JSON->message .= "IP: " . var_export($_SERVER['REMOTE_ADDR'], true);
}

?>