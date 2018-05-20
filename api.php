<?php

// For debug-purposes only!
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';
require_once('UKMrfid.php');

$log = UKMNorge\UKMrfid::getLogger();

$log->debug("Starting router...");


?>