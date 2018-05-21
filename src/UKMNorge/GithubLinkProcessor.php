<?php

namespace UKMNorge;

use Monolog\Logger;

/**
 * Must be run after IntroSpectionProcessor to work properly, as this
 * uses the record generated from there to build Github links to the source.
 * 
 * @author A. Hustad
 */
class GithubLinkProcessor
{
	private $username;
	private $repository;

	public function __construct( $username, $repository ) {
		$this->username = $username;
		$this->repository = $repository;

		$this->root_folder = basename(__DIR__);
	}

	public function __invoke(array $record) {
		$file = $record['extra']['file'];
		$line = $record['extra']['line'];

		$strip_start = strpos($file, $this->root_folder) + strlen($this->root_folder)+1;
		$file = substr($file, $strip_start);

		$URL = "http://github.com/".$this->username."/".$this->repository."/blob/master/".$file."#L".$line;

		$record['message'] .= " \nLogged at <".$URL.">";

    	return $record;
    }	
}