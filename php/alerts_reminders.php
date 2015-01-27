<?php
require_once("mysql_interface.php");
require_once("misc.php");
require_once("users.php");

class AL_REM
{
	public $mInterface;
	public $misc;
	public $users;
	
	public function __construct() {
		$this->mInterface = new MYSQL_INTERFACE();
		$this->misc = new MISC();
		$this->users = new USERS();
	}
	
	public function getAlerts_byModule($module) {
		return $this->mInterface->ar_getAlerts_byModule($module);
	}
	
	public function getReminders_byModule($module) {
		return $this->mInterface->ar_getReminders_byModule($module);
	}
	
	public function get_modules_alerts_reminders() {
		$modules = $this->misc->getModules();
		
		foreach($modules as $key=>&$value) {
			$value['alerts'] = $this->getAlerts_byModule($key);
			$value['reminders'] = $this->getReminders_byModule($key);
		}
		
		return $modules;
	}
	
	public function save($postdata, $id = false) {
		$str = array("username='" . $this->mInterface->escapeString($_SESSION['user']) . "'");
		
		foreach($postdata as $key=>$value) {
			array_push($str, $key."='".$this->mInterface->escapeString($value)."'");
		}
		
		$queryPart = join(",", $str);
		
		if($id) {
			$result = $this->mInterface->ar_updateSchedule($queryPart, $id);
		} else {
			$result = $this->mInterface->ar_saveSchedule($queryPart);
		}
		
		if($result) return "SUCCESS";
		else return "ERROR";
	}
	
	public function get() {
		$result = $this->mInterface->ar_getScheduleByUser($_SESSION['user']);
		if($result) return $result[0];
		else return "ERROR";
	}
	
	public function getUsersDetailsByAlertID($id) {
		$id = intval($id);
		
		$users = $this->mInterface->ar_getScheduleByAlert($id);
		
		$usersDetails = array();
		for($i=0; $i<count($users); $i++) {
			$details = $this->users->getUser($users[$i]['username']);
			array_push($usersDetails, $details[0]);
		}
		
		return $usersDetails;
	}
}
?>