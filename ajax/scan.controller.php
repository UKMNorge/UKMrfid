<?php

require_once('UKM/RFID/person.collection.php');
require_once('UKM/RFID/scan.class.php');

use UKMNorge\RFID\Scan;
use UKMNorge\RFID\PersonColl;
use UKMNorge\RFID\Person;
use UKMNorge\RFID\Herd;

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
		$person = PersonColl::getByRFID( $rfid );
		$JSON->person = $person->getFirstName();
		$JSON->herd = $person->getHerd()->getName();
		$JSON->herd_foreign_id = $person->getHerd()->getForeignId();
		$JSON->direction = $scanner->getDirection();
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

