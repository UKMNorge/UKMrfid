<?php

require_once('UKM/RFID/person.collection.php');
require_once('UKM/RFID/scan.class.php');

use UKMNorge\RFID\Scan;

global $scanner;
global $guid;

// Debug
$JSON->data = $_POST;

$rfid = $_POST['rfidValue'];

// Lag hash av rfid og IP for å sjekke om dette er en OK request
$hash = sha1($rfid + $guid);
if ( true || $hash == $_POST['hash'] ) {
	try {
		$scan = Scan::create($rfid, $scanner->getDirection(), $scanner->getAreaId());
		$JSON->success = true;
	} 
	catch (Exception $e) {
		$JSON->success = false;
		$JSON->message = $e->getMessage();
		$JSON->code = $e->getCode();
	}
} else {
	$JSON->success = false;
	$JSON->message = "RFID og GUID-hash matcher ikke!";
	$JSON->data['lokalHash'] = $hash;
}

