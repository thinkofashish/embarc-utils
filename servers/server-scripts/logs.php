<?php

define("LOG_FILES_PATH", "/root/findnsecure/");

class LOGS
{	
	public function getCompleteLog($path) {
		$path = LOG_FILES_PATH . $path;
		//get log file using runscript
		return `./runscript c 0 $path`;
	}
	
	public function getPartLog($path) {
		$path = LOG_FILES_PATH . $path;
		
		//get tail 2 lines using runscript, which runs as root - yay!
		return `./runscript p 2 $path`;
	}
}
?>