<?php

// For debug-purposes only!
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';
require_once('../UKMrfid.php');

require_once('UKMconfig.inc.php');
require_once('UKM/postgres.class.php');
require_once('UKM/RFID/scanner.collection.php');

UKMNorge\RFID\POSTGRES::connect( PG_RFID_USER, PG_RFID_PASS, PG_RFID_DB );

$log = UKMNorge\UKMrfid::getLogger();

$log->debug("Starting router...");

$JSON = new stdClass();

// Kun gjør routing om man gjør POST requests
if('POST' == $_SERVER['REQUEST_METHOD']) {
	$endpoint = $_POST['endpoint'];
	$guid = $_POST['guid'];
	$scanners = new \UKMNorge\RFID\ScannerColl();

	try {
		// Hvis endpoint er "registerStation" eller "verifyStation", trenger vi ikke autentisering av GUID.
		if ('registerStation' == $endpoint || 'verifyStation' == $endpoint) {
			require_once('../ajax/'. $endpoint .'.controller.php');	
		}
		elseif( $scanner = $scanners->getByGUID($guid) && $scanner->isVerified() ) {
			$controller = '../ajax/'. $endpoint .'.controller.php';
			if( !file_exists( $controller ) ) {
				$JSON->success = false;
				$JSON->message = 'Mangler kontroller '.$controller.'!';
			} 
			else {
				require_once('../ajax/'. $endpoint .'.controller.php');
			}
		}
		else {
			$JSON->success = false;
			$JSON->message = 'Stasjonen du bruker har ikke tilgang enda - kontakt UKM Support';
		}
	} catch( Exception $e ) {
		$JSON->success = false;
		$JSON->message = $e->getMessage();
		$JSON->code = $e->getCode();
	}		


	$json_encoded = json_encode($JSON);

	header('Content-Type: application/json');
	echo $json_encoded;
	die();
}


?>