<?php

namespace UKMNorge;

class UKMrfid {
	/**
	 * Returns true if we are in devmode, false if not.
	 *
	 */
	public static function dev() {
		return UKM_HOSTNAME == 'ukm.dev';
	}

	/**
	 * Sets up a logger in the pre-configured environment.
	 * Mostly to keep index.php clean.
	 *
	 */
	public static function getLogger() {
		require_once "UKMconfig.inc.php";

		$log = new \Monolog\Logger('UKMrfid');
		try {
			// If we get a warning, log the entire request.
			// If there are only INFO's or lower, don't log to file.
			$fileHandler = new \Monolog\Handler\StreamHandler('/tmp/ukmrfid/app.log', \Monolog\Logger::DEBUG);

			/*$fingersCrossedHandler = new \Monolog\Handler\FingersCrossedHandler($fileHandler, \Monolog\Logger::WARNING);
*/
			// Add handlers - the last one added will be the first to receive events.
			$log->pushHandler($fileHandler);
			//$log->pushHandler($fingersCrossedHandler);

			// Add processor for line information
			$log->pushProcessor(new \Monolog\Processor\IntrospectionProcessor());

			// Do a logging test / first info
			$log->info('Initialized logger.');

			return $log;
		}
		catch (Exception $e) {
			if( self::dev() ) {
				error_log("UKMRFID: UNABLE TO START LOGGER - EXITING. Message: ".$e->getMessage());
				die("UKMRFID: UNABLE TO START LOGGER - EXITING. Message: ".$e->getMessage());
			}
			// TODO: Add error handling in production - return JSON!
			die("");
		}
	}
}

?>