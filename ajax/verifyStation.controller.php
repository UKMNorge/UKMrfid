<?php

// Station er definert i api.php
global $station;

if ( $station->isVerified( $_POST['guid'] ) ) {
	$JSON->success = true;
	$JSON->message = "Stasjon godkjent!";
} else {
	$JSON->success = false;
	$JSON->message = "Stasjon ikke godkjent enda";
}
?>