<?php
class SETTINGS
{
	public $mInterface;
	
	public function __construct() {
		$this->mInterface = new MYSQL_INTERFACE();
	}
	
	public function getConstants() {
		$file = file('CONSTANTS.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$settings = array();
		
		for($i = 0; $i < count($file); $i++) {
			//remove line endings, if exists
			$file[$i] = rtrim($file[$i]);
			
			//ignore comments
			if(substr($file[$i], 0, 2) == "//") continue;
			
			//split on space
			$row = explode(' ', $file[$i]);
			$settings[$row[0]] = $row[1];
		}
		return $settings;
	}
	
	public function getCSSettings() {
		$settings = $this->mInterface->getCSSettings();
		return $settings[1];
	}
	
	public function setCSSettings($settingsJSON) {
		if($this->mInterface->saveCSSettings("DHL", $settingsJSON)) return "SUCCESS";
		else return "ERROR";
	}
}
?>