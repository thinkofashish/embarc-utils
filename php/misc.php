<?php
require_once("mysql_interface.php");

class MISC
{
	public $mInterface;
	
	public function __construct() {
		$this->mInterface = new MYSQL_INTERFACE();
	}
	
	// returns detailed list of all modules
	public function listModules() {
		return $this->mInterface->misc_getAllModules();
	}
	
	// returns detailed list of modules for current user
	public function getModules() {
		$modules = $this->listModules();
		$allModules = array();
		foreach($modules as $k=>$v) {
			$allModules[$v["id"]] = $v;
		}
		
		$allowedModules;
		$userModules = $this->mInterface->misc_getUserModules($_SESSION["user"]);
		if($userModules == null) {
			$allowedModules = "ERROR";
		} else {
			//get first user only
			$userModules = $userModules[0];
			
			$allowedModules = array();
			$userModules = explode(",", $userModules);
			for($i=0; $i<count($userModules); $i++) {
				if(isset($allModules[$userModules[$i]])) {
					$allowedModules[$userModules[$i]] = $allModules[$userModules[$i]];
				}
			}
		}
		
		return $allowedModules;
	}
	
	// returns preferences for current user for specified module
	public function getPreferences($module) {
		$result = $this->mInterface->misc_getPreferences($_SESSION["user"], $module);
		return json_decode($result[0]);
	}
	
	// save preferences for current user for specified module
	public function savePreferences($module, $postData) {
		if($this->mInterface->misc_savePreferences($_SESSION["user"], $module, json_encode($postData))) {
			return "success";
		} else {
			return "error";
		}
	}
}
?>