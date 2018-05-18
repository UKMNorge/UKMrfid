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
			$fileHandler = new \Monolog\Handler\StreamHandler('/tmp/ukmrfid/app.log', \Monolog\Logger::INFO);
			$browserConsoleHandler = new \Monolog\Handler\browserConsoleHandler();

			// Default log level for the Slack Handler to be triggered is CRITICAL
			$slackHandler = new \Monolog\Handler\SlackWebhookHandler(SLACK_UKMRFID_WEBHOOK_URL, SLACK_UKMRFID_CHANNEL);

			// Add handlers - the last one added will be the first to receive events.
			$log->pushHandler($fileHandler);
			if ( self::dev() ) $log->pushHandler($browserConsoleHandler);
			$log->pushHandler($slackHandler);

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