<?php
require_once("mysql_interface.php");

class SERVER
{
	public $mInterface;
	
	public function __construct() {
		$this->mInterface = new MYSQL_INTERFACE();
	}
	
	// get list of servers
	public function getServersList() {
		return $this->mInterface->sm_getServers();
	}
	
	// get details of a server
	public function getServer($id) {
		return $this->mInterface->sm_getServerDetails($id);
	}
	
	// add/update a server
	public function addServer($data, $id = false) {
		$str = array();
		
		foreach($data as $key=>$value) {
			array_push($str, $key."='".$this->mInterface->escapeString($value)."'");
		}
		
		$queryPart = join(",", $str);
		
		if($id) {
			$result = $this->mInterface->sm_updateServer($queryPart, $id);
		} else {
			$result = $this->mInterface->sm_addServer($queryPart);
		}
		
		if($result) return "SUCCESS";
		else return "ERROR";
	}
	
	// remove server - not exactly remove, sort of vanish
	public function removeServer($id) {
		if($this->mInterface->sm_removeServer($id, $_SESSION['user'])) {
			return "SUCCESS";
		} else {
			return "ERROR";
		}
	}
	
	// get status of a server by IP
	public function getStatus($ip, $tcInt=NULL) {
		
		$params = "";
		if(isset($tcInt) && $tcInt != 0) $params = "?tcInt=" . $tcInt;
		
		//The cURL method
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://$ip/status/status.php" . $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$data = curl_exec($ch);
		curl_close($ch);
		
		/*
		* Alternate method
		* file_get_contents() method
		*/
		//$data = file_get_contents("http://$ip/status/status.php");
		
		return $data;
	}
	
	// verify AES passphrase
	public function verify_AES_passphrase($hash) {
		$passphrase = "The key is, having admin privileges. Bazinga!";

		if($hash == hash('sha256', $passphrase)) return "SUCCESS";
		else return "ERROR";
	}
	
	// get list of data centres
	public function getDC() {
		return $this->mInterface->sm_getDatacentres();
	}
	
	// add a data centre
	public function addDC($data) {
		$str = array();
		
		foreach($data as $key=>$value) {
			array_push($str, $key."='".$this->mInterface->escapeString($value)."'");
		}
		
		$queryPart = join(",", $str);
		
		$result = $this->mInterface->sm_addDatacentre($queryPart);
		
		if($result) return "SUCCESS";
		else return "ERROR";
	}
}
?>