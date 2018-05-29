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

			$fingersCrossedHandler = new \Monolog\Handler\FingersCrossedHandler($fileHandler, \Monolog\Logger::WARNING);

			// Default log level for the Slack Handler to be triggered is CRITICAL
			$slackHandler = new \Monolog\Handler\SlackWebhookHandler(SLACK_UKMRFID_WEBHOOK_URL, SLACK_UKMRFID_CHANNEL, 'UKMrfid');
			
			// Add links to the source code for Slack-integration.
			#$slackHandler->pushProcessor(new \UKMNorge\GithubLinkProcessor('AsgeirSH', 'UKMrfid'));

			// A Deduplicatior will limit the amount of slack notifications sent to one per minute.
			/*$slackDeduplicator = new \Monolog\Handler\DeduplicationHandler(
				$slackHandler, 
				"/tmp/ukmrfid/deduplication_logs",
				\Monolog\Logger::CRITICAL
			);*/

			// Add handlers - the last one added will be the first to receive events.
			$log->pushHandler($fileHandler);
			//$log->pushHandler($slackDeduplicator);

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