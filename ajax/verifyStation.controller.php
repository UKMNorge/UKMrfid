<?php

// Stations er definert i api.php
if ( $stations->isVerified( $_POST['guid'] ) ) {
	$JSON->success = true;
	$JSON->message = "Stasjon godkjent!";
} else {
	$JSON->success = false;
	$JSON->message = "Stasjon ikke godkjent enda";
}
?>