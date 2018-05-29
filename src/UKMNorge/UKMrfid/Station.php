<?php

namespace UKMNorge\UKMrfid;

require_once('UKM/sql.class.php');

/** 
 * Definerer et sett godkjente stasjoner, med hjelpefunksjoner 
 * for alle requests.
 *
 */
class Station {
	
	private $log;
	private $guid; // Settes ved construct.
	
	private $station = null; // Lastes inn av _load();
	
	/**
	 * Laster inn data fra SQL, bygger lokale lister over stasjoner.
	 *
	 */
	public function __construct($guid) {

		$this->log = \UKMNorge\UKMrfid::getLogger();

		$this->guid = $guid;

		$this->_load();
		
	}

	private function _load() {

		$qry = new \SQL('SELECT * FROM #table WHERE `guid` = "#guid"', array('table' => "rfid_stations", 'guid' => $this->guid));

		$res = $qry->run('array');

		if ( false == $res ) {
			$this->log->notice('Fant ikke stasjon med rett ID i tabellen');
			return false;
		}

		$obj = new \StdClass();
		$obj->id = $res['id'];
		$obj->guid = $res['guid'];
		$obj->ip = $res['ip'];
		$obj->valid = $res['verified'];
		$obj->registered = $res['registerTime'];
				
		// Lookup er normalt gjort med GUID.
		$this->station = $obj;
	}

	/** 
	 * Returnerer true dersom stasjonen er godkjent og har lov til å 
	 * registrere passeringer, og IP'en som er registrert stemmer med
	 * IP'en du kobler til med.
	 *
	 */
	public function isVerified() {
		if ( $this->station == null ) {
			return false;
		}

		if ($_SERVER['REMOTE_ADDR'] == $this->station->ip ) {
			return $this->station->valid;
		}
		$this->log->info("En stasjon forsøkte å verifisere seg uten å finnes i stasjonstabellen.", array('guid' => $this->guid, 'ip' => $_SERVER['REMOTE_ADDR']));
		return false;
	}

	/**
	 * Registrerer en ny stasjon i databasen.
	 * 
	 * 
	 */ 
	public function registerNew() {
		$this->log->info("Registrerer stasjon i databasen.");

		$ip = $_SERVER['REMOTE_ADDR'];

		$qry = new \SQLins('rfid_stations');	
		
		$qry->add('guid', $this->guid);
		$qry->add('registerTime', date_create()->format('Y-m-d H:i:s'));
		$qry->add('ip', $ip);

		$res = $qry->run();

		if (-1 == $res || 0 == $res ) {
			$this->log->critical("Feilet på å sette inn rad i databasen!", array("guid" => $guid, "ip" => $ip) );
			return false;
		}
		return true;
	}
}

?>