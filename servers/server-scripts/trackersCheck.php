<?php
require_once "mysql_db.php";

class TR_UPDATES
{
	public $db_object = false;
	public $db_connection = false;
	public $RECENT_SECONDS = 3600; // 1 hour

	public function __construct($custom_seconds) {
		$this->db_object = new MYSQL_DB();
		$this->db_connection = $this->db_object->connect();
		
		// custom seconds override
		if(isset($custom_seconds)) $this->RECENT_SECONDS = intval($custom_seconds);

		// set default timezone to UTC
		date_default_timezone_set('UTC');
	}

	public function getTrackersList() {
		$query = "SELECT trackerid from trackers";
		$result = $this->db_object->query_db($query);

		return $this->db_object->getResultArray();
	}

	public function getLastRecord($trackerid) {
		$query = "SELECT UNIX_TIMESTAMP(pdatetime) as pdatetime from A" . $trackerid . " order by pdatetime desc limit 0,1";
		$this->db_object->query_db($query);

		return $this->db_object->getResultRow();
	}

	public function checkRecord($trackerid) {
		$lastRecord = $this->getLastRecord($trackerid);

		if(is_array($lastRecord)) {
			if(time() - $lastRecord[0] < $this->RECENT_SECONDS) {
				return true;
			}
			return false;
		}
	}

	public function checkTrackers() {
		$trackers = $this->getTrackersList();
		$result = array("count"=>count($trackers), "interval"=>$this->RECENT_SECONDS, "status"=>-1);

		for($i = 0; $i < count($trackers); $i++) {
			if($this->checkRecord($trackers[$i])) {
				$result["status"] = 1;

				break;
			}
		}
		return $result;
	}
}
?>