<?php
require_once("mysql_db.php");

class MYSQL_INTERFACE
{
	public $db_object = false;
	public $db_connection = false;
	
	public function __construct() {
		$this->db_object = new MYSQL_DB();
		$this->db_connection = $this->db_object->connect();
	}
	
	public function getCountries() {
		$query = "SELECT * from cs_dhlZones";
		$result = $this->db_object->query_db($query);
		$countries = array();
		while($row = $result->fetch_assoc()) {
			array_push($countries, $row);
		}
		$result->free();
		
		return json_encode($countries);
	}
	
	public function getZone($countryCode) {
		$query = "SELECT * from cs_dhlZones where code='".$countryCode."'";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function getUser($username) {
		$query = "SELECT * from users where username='".$this->db_object->escapeString($username)."'";
		$result = $this->db_object->query_db($query);
		if($result == false) return false;
		
		$row = $result->fetch_assoc();
		$result->free();
		
		return $row;
	}
	
	public function getDHLPrice($account, $zone, $type, $weight) {
		$query = "SELECT `$zone` from cs_dhl$account where type='".$type."' and weight >= $weight";		
		$this->db_object->query_db($query);
		$result = $this->db_object->getResultRow();
		
		return $result;
	}
	
	public function getAllAccounts() {
		$query = "SELECT * from cs_accounts";
		$this->db_object->query_db($query);
		$accounts = $this->db_object->getResultSet();
		
		return $accounts;
	}
	
	public function saveCSSettings($serviceName, $settingsJSON) {
		$serviceName = $this->db_object->escapeString($serviceName);
		$settingsJSON = $this->db_object->escapeString($settingsJSON);
		if($this->getCSSettings() == false) {
			$query = "INSERT INTO cs_settings set serviceName='".$serviceName."', preferences='".$settingsJSON."'";
		} else {
			$query = "UPDATE cs_settings set preferences='".$settingsJSON."' where serviceName='".$serviceName."'";
		}
		return $this->db_object->query_db($query);
	}
	
	public function getCSSettings() {
		$query = "SELECT * from cs_settings";
		$this->db_object->query_db($query);
		return $this->db_object->getResultRow();
	}
	
	public function in_getTrackers() {
		$query = "SELECT * from in_trackers";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function in_getStock($field, $val) {
		$query = "SELECT * from in_stock where $field='".$val."'";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function in_addStockItem($queryPart) {
		$query = "INSERT INTO in_stock set ".$queryPart;
		
		return $this->db_object->query_db($query);
	}
	
	public function in_updateStockItem($queryPart, $id) {
		$query = "UPDATE in_stock set ".$queryPart. " where id=$id";
		
		return $this->db_object->query_db($query);
	}
	
	public function in_getItemInStock($field, $val) {
		$query = "SELECT * from in_stock where inStock=1 and $field='".$val."'";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function in_matchStock($isCount, $queryPart) {
		$query = "";
		if($isCount == 1) {
			$query = "SELECT count(*) from in_stock where ";
		} else {
			$query = "SELECT * from in_stock where ";
		}
		$query .= $queryPart;
				
		$this->db_object->query_db($query);
		
		if($isCount == 1) {
			return $this->db_object->getResultRow();
		} else {
			return $this->db_object->getResultSet();
		}
		
	}
	
	public function in_addClient($queryPart) {
		$query = "INSERT INTO in_clients set ".$queryPart;
		
		return $this->db_object->query_db($query);
	}
	
	public function in_addTracker($queryPart) {
		$query = "INSERT INTO in_trackers set ".$queryPart;
		
		return $this->db_object->query_db($query);
	}
	
	public function in_getClients() {
		$query = "SELECT * from in_clients";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function misc_getUserModules($username) {
		if($username === 'master') return $this->misc_getMasterModules();
		$query = "SELECT modules from users where username='".$username."'";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultRow();
	}

	// Always bow down for master
	public function misc_getMasterModules() {
		$query = "SELECT id from modules";
		$this->db_object->query_db($query);

		$modules = array();
		$modulesSet = $this->db_object->getResultSet();
		for($i = 0; $i < count($modulesSet); $i++) {
			array_push($modules, $modulesSet[$i]['id']);
		}
		
		return array(join(",", $modules));
	}
	
	public function misc_getAllModules() {
		$query = "SELECT * from modules";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function misc_getPreferences($username, $module) {
		$query = "SELECT preference from preferences where username='".$username."' and module=".$module;
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultRow();
	}
	
	public function misc_savePreferences($username, $module, $prefsJSON) {
		$prefsJSON = $this->db_object->escapeString($prefsJSON);
		
		if($this->misc_getPreferences($username, $module)) {
			$query = "UPDATE preferences set preference='".$prefsJSON."' where username='".$username."' and module=".$module;
		} else {
			$query = "INSERT INTO preferences set username='".$username."',module=".$module.",preference='".$prefsJSON."'";
		}
		
		return $this->db_object->query_db($query);
	}
	
	public function sm_addServer($queryPart) {
		$query = "INSERT INTO sm_servers set ".$queryPart;
		
		return $this->db_object->query_db($query);
	}
	
	public function sm_updateServer($queryPart, $id) {
		$query = "UPDATE sm_servers set " . $queryPart . " where id=" . $id;
		
		return $this->db_object->query_db($query);
	}
	
	public function sm_getServers() {
		$query = "SELECT * from sm_servers where removedBy is NULL";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function sm_removeServer($id, $username) {
		$query = "UPDATE sm_servers set removedBy='" . $username . "' where id=" . $id;
		
		return $this->db_object->query_db($query);
	}
	
	public function sm_getServerDetails($id) {
		$query = "SELECT * from sm_servers where id=" . $id;
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function sm_getDatacentres() {
		$query = "SELECT * from sm_datacentres";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function sm_addDatacentre($queryPart) {
		$query = "INSERT INTO sm_datacentres set ".$queryPart;
		
		return $this->db_object->query_db($query);
	}
	
	public function us_addUser($queryPart) {
		$query = "INSERT INTO users set ".$queryPart;
		
		return $this->db_object->query_db($query);
	}
	
	public function us_updateUser($queryPart, $username) {
		$query = "UPDATE users set " . $queryPart . " where username='" . $this->escapeString($username) . "'";
		
		return $this->db_object->query_db($query);
	}
	
	public function us_getUsers() {
		$query = "SELECT * from users";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function us_removeUser($username) {
		$query = "DELETE from users where username='" . $this->escapeString($username) . "'";
		
		return $this->db_object->query_db($query);
	}
	
	public function us_getUserDetails($username) {
		$query = "SELECT * from users where username='" . $this->escapeString($username) . "'";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function ar_getAlerts_byModule($module) {
		$query = "SELECT * from _ar_alertsTypes where module=".$module;
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function ar_getReminders_byModule($module) {
		$query = "SELECT * from _ar_remindersTypes where module=".$module;
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function ar_saveSchedule($queryPart) {
		$query = "INSERT INTO ar_schedules set " . $queryPart;
		
		return $this->db_object->query_db($query);
	}
	
	public function ar_updateSchedule($queryPart, $id) {
		$query = "UPDATE ar_schedules set " . $queryPart . " where id=" . $id;
		
		return $this->db_object->query_db($query);
	}
	
	public function ar_getScheduleByAlert($alertID) {
		$query = "SELECT * from ar_schedules where alerts & (1 << $alertID)";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function ar_getScheduleByReminder($reminderID) {
		$query = "SELECT * from ar_schedules where reminders & (1 << $reminderID)";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function ar_getScheduleByUser($username) {
		$query = "SELECT * from ar_schedules where username='$username'";
		$this->db_object->query_db($query);
		
		return $this->db_object->getResultSet();
	}
	
	public function escapeString($str) {
		return $this->db_object->escapeString($str);
	}
}
?>
