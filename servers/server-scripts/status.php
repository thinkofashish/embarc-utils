<?php
/*
* 06 November, 2013
* server status v2.0.4 Tushar Agarwal niftytushar@gmail.com
*
* server script for server status utility
* To modify processes change process names in $processes array()
* Default disk type is ext4, change disk type in $disks (backticks) array()
*/

// include required files
require_once "trackersCheck.php";
require_once "logs.php";

$log_obj = new LOGS();
if(isset($_GET["fp"])) {
	$filename = $_GET["fp"];
	header('Content-type: text/plain');
	header("Content-Disposition: attachment; filename=" . $filename . ".txt");
	echo $log_obj->getCompleteLog($filename);
	return;
}

function createArray($data) {
	//create array for each line
	$data = explode("\n", trim($data, "\n"));
	//skip headers and create array from each remaining row
	for($i=1; $i<count($data); $i++) {
		$result[] = preg_split("/[\s]+/", $data[$i]);
	}
	return $result;
}
/*
* get RAM details
*/
$mem = createArray(`free -m`);

/*
* get HDD details, of partition type ext4
*/
$disks = createArray(`df -text4 -BM`);

/*
* get status of required processes
*/
function getProcessStatus($pname) {
	$result = `ps -eo "%p %c" | grep $pname`;
	if($result) {
		return 1;
	} else {
		return -1;
	}
}
$processes = array("udp", "frontend", "mailer", "geofence", "rfidman");
$proc = array();
for($i=0; $i<count($processes); $i++) {
	$proc[$processes[$i]] = getProcessStatus($processes[$i]);
}

/*
* read log files
*/
$log_files = array("frontend.log", "mailer.log", "geofence.log", "udp_server.log", "rfid.log");
$logs = array();
for($i=0; $i<count($log_files); $i++) {
	$logs[$log_files[$i]] = $log_obj->getPartLog($log_files[$i]);
}

/*
* check if trackers are working/updating
*/

// if custom check interval is provided (in seconds)
$seconds = NULL;
if(isset($_GET["tcInt"])) $seconds = $_GET["tcInt"];

$updates = new TR_UPDATES($seconds);
$areUpdating = $updates->checkTrackers();

/*
* Get statisticts of udp server - gStatistics
*/
function getStats($timeout =1)
{
	$query = "gStatistics\r\n";
	$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if (!is_resource($sock)) {
		return socket_strerror(socket_last_error()).PHP_EOL;
	}

	$timeout = array('sec' => $timeout, 'usec'=>0);
	if (!socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, $timeout)){
		return socket_strerror(socket_last_error()).PHP_EOL;
	}

	if (!socket_connect($sock, "localhost", 42006)) {
		return socket_strerror(socket_last_error()).PHP_EOL;
	}

	$data = '';

	socket_write($sock, $query,strlen($query));
	while(1) {
		$d = socket_read($sock, 255, PHP_BINARY_READ);
		if ($d == FALSE) break;
		$data .= $d;
	}
	return json_decode($data);
}

//create associative array of disks and RAM
$data = array("disk"=>$disks, "mem"=>$mem, "process"=>$proc, "logs"=>$logs, "updating"=>$areUpdating, "gStats"=>getStats());

echo json_encode($data);
?>
