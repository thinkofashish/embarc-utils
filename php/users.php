<?php
require_once("mysql_interface.php");

class USERS
{
	public $mInterface;
	public $LOG_FILE = "/var/www/embarc-utils/logs/users.log";
	
	public function __construct() {
		$this->mInterface = new MYSQL_INTERFACE();
		
		// clear the .log file initially
		file_put_contents($this->LOG_FILE, "", FILE_APPEND | LOCK_EX);
	}
	
	// save a new user
	public function saveUser($postData, $username = false) {
		$str = array();
		
		foreach($postData as $key=>$value) {
			array_push($str, $key."='".$this->mInterface->escapeString($value)."'");
		}
		
		$queryPart = join(",", $str);
		
		if($username) {
			$result = $this->mInterface->us_updateUser($queryPart, $username);
		} else {
			$result = $this->mInterface->us_addUser($queryPart);
		}
		
		// update current user's modules in SESSION, if required
		if($_SESSION["user"] == $username) $_SESSION['modules'] = $postData["modules"];
		
		if($result == "1") {
			$this->do_log("User details updated: $username");
			return "SUCCESS";
		} else if($result == "") return "EXISTS";
		else return "ERROR";
	}
	
	// get list of users
	public function getUsersList() {
		return $this->mInterface->us_getUsers();
	}
	
	// get single user details by username
	public function getUser($username) {
		return $this->mInterface->us_getUserDetails($username);
	}
	
	// remove a user by username
	public function remove($username) {
		if($this->mInterface->us_removeUser($username)) {
			$this->do_log("User $username removed.");
			
			return "SUCCESS";
		} else {
			return "ERROR";
		}
	}
	
	public function do_log($data) {
		file_put_contents($this->LOG_FILE, date("r")." - ".$data."\r\n", FILE_APPEND | LOCK_EX);
	}
}
?>