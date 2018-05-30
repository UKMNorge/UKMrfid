<?php

require_once('UKM/RFID/person.collection.php');

global $scanner;
global $guid;

// Debug
$JSON->data = $_POST;

$rfid = $_POST['rfidValue'];

// Lag hash av rfid og IP for å sjekke om dette er en OK request
$hash = sha1($rfid + $guid);
if ( $hash != $_POST['hash'] ) {
	$JSON->success = false;
	$JSON->message = "RFID og GUID-hash matcher ikke!";
	$JSON->data['lokalHash'] = $hash;
} 
else if( PersonColl::hasRFID($rfid) ) {
	$person = PersonColl::getByRFID($rfid);
	try {
		$scan = Scan::create($rfid, $scanner->getDirection(), $scanner->getArea());
		$JSON->success = true;
	} 
	catch (Exception $e) {
		$JSON->success = false;
		$JSON->message = $e->getMessage();
		$JSON->code = $e->getCode();
	}
	

} else {
	$JSON->success = false;
	$JSON->message = "Fant ikke personen du prøvde å scanne!";
}