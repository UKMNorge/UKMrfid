<?php

// Har vi scanner?
global $scanners;
global $guid;
try {
	$scanner = $scanners->getByGUID($guid);	
	if ( $scanner->isVerified() ) {
		$JSON->success = true;
		$JSON->message = "Stasjon godkjent!";
	}
	else {
		$JSON->success = false;
		$JSON->message = "Stasjon ikke godkjent enda";
	}
}
catch (Exception $e) {
	$JSON->success = false;
	$JSON->message = "Stasjon ikke funnet!";
}


?>