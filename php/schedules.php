<?php
require_once "mysql_interface.php";
require_once "servers.php";
require_once "smtp.php";
require_once "alerts_reminders.php";

$schedules = new SCHEDULES();
$schedules->checkAllServers();

class SCHEDULES
{
	public $mInterface;
	public $servers;
	public $smtp_obj;
	public $al_rem;
	public $options;
	public $LOG_FILE = "/var/www/embarc-utils/logs/schedule.log";

	public function __construct() {
		$this->mInterface = new MYSQL_INTERFACE();
		$this->servers = new SERVER();
		$this->smtp_obj = new SMTPs();
		$this->al_rem = new AL_REM();
		
		$shortopts = "a:";			// Alert ID - 1
		//$shortopts .= "r:";			// Reminder ID - 1
		
		// reading options from CLI
		$this->options = getopt($shortopts);
		
		// clear the .log file initially
		file_put_contents($this->LOG_FILE, "", FILE_APPEND | LOCK_EX);
	}
	
	public function checkAllServers() {
		$list_of_servers = $this->servers->getServersList();
		$mailBody = '<html>
					<head></head>
					<body>';
		$errorCount = 0;
		
		for($i = 0; $i < count($list_of_servers); $i++) {
			$server_status = $this->servers->getStatus($list_of_servers[$i]["ip_address"]);
			$server_status = json_decode($server_status);
			
			if(gettype($server_status) != "object") { // unable to connect to server
				$status = array("updating"=>-1, "info"=>$list_of_servers[$i]);
				$mailBody .= $this->getBody_UC($status);
				$errorCount++;
			} else {
				if($server_status->updating->status == -1) { // AVL data not received
					$status = array("updating"=>$server_status->updating, "info"=>$list_of_servers[$i]);
					$mailBody .= $this->getBody_AVL($status);
					$errorCount++;
				}
			}
		}
		
		$mailBody .= '</body>
					</html>';
		
		
		// alert id 0, for hourly alerts is stored in options
		$recipients = $this->al_rem->getUsersDetailsByAlertID($this->options["a"]);
		
		// finally send a mail
		$result = $this->smtp_obj->sendMail(null, $recipients, "$errorCount - Server Down", $mailBody, 0);
		
		if($result == "SUCCESS") $this->do_log("scheduled server check report sent.");
		else $this->do_log($result);
	}
	
	public function getBody_UC($status) {
		return '<p>
		Unable to connect to server <b>' . $status["info"]["company"] . '</b> </p>
		<table>
		<tr><td>Contact Name</td><td>'.$status["info"]["contact"].'</td></tr>
		<tr><td>Email ID</td><td>'.$status["info"]["email"].'</td></tr>
		<tr><td>IP</td><td>'.$status["info"]["ip_address"].'</td></tr>
		<tr><td>URL</td><td><a href="http://' . $status["info"]["url"] . '">'.$status["info"]["url"].'</a></td></tr>
		<tr><td>Software Version</td><td>'.$status["info"]["sw_version"].'</td></tr>
		</table><br /><br />';
	}
	
	// get mail body for servers with errors
	public function getBody_AVL($status) {
		return '<p>
		AVL data was <b>NOT</b> received on the server <b>' . $status["info"]["company"] . '</b> in last ' . intval($status["updating"]->interval) / 60 . ' minutes. </p>
		<table>
		<tr><td>Contact Name</td><td>'.$status["info"]["contact"].'</td></tr>
		<tr><td>Email ID</td><td>'.$status["info"]["email"].'</td></tr>
		<tr><td>IP</td><td>'.$status["info"]["ip_address"].'</td></tr>
		<tr><td>URL</td><td><a href="http://' . $status["info"]["url"] . '">'.$status["info"]["url"].'</a></td></tr>
		<tr><td>Software Version</td><td>'.$status["info"]["sw_version"].'</td></tr>
		</table><br /><br />';
	}
	
	public function do_log($data) {
		file_put_contents($this->LOG_FILE, date("r")." - ".$data."\r\n", FILE_APPEND | LOCK_EX);
	}
}
?>