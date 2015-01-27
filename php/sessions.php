<?php
session_start();

class SESSIONS
{
	public $REDIRECT_URL = "/embarc-utils/login.html";
	public $DASHBOARD_URL = "/embarc-utils/dashboard.php";
	
	public function check() {
		if(isset($_SESSION['user'])) {
			$user = $_SESSION['user'];
			return true;
		} else {
			$this->destroy();
			header("Location: ".$this->REDIRECT_URL);
			return false;
		}
	}
	
	// check if a module is authorized to the current user, if not - goto dashboard
	public function isModuleAuthorized($module) {
		$userModules = $_SESSION['modules'];
		$userModules = explode(",", $userModules);
		
		for($i=0; $i<count($userModules); $i++) {
			if($module == $userModules[$i]) {
				return true;
			}
		}
		
		// if module is not authorized
		header("Location: ".$this->DASHBOARD_URL);
		return false;
	}
	
	public function destroy() {
		session_destroy();
	}
}
?>