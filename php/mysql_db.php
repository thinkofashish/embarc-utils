<?php
require_once('configuration.php');

class MYSQL_DB
{
	public $connection = false;
	public $result = false;
	
	function connect() {
		global $DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME;
	
		//if database connection is already established, close the connection
		if($this->connection != false) {
			$this->disconnect();
		}

		$this->connection = new mysqli($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
		if($this->connection->connect_error) {
			die("Connect Error (".$this->connection->connect_errno.") ".$this->connection->connect_error);
		}
		return $this->connection;
	}
	
	function disconnect() {
		if($this->connection == false) return;
		$this->connection->close();
		$this->connection = false;
	}
	
	function query_db($query) {
		$this->result = $this->connection->query($query);
		
		if(!$this->result) $this->result = $this->connection->error;
		
		return $this->result;
	}
	
	function getResultRow() {
		if($this->result == false) return false;
		
		$row = $this->result->fetch_row();
		if($row == false) {
			$this->result->free();
			$this->result = false;
		}
		
		return $row;
	}
	
	function getResultSet() {
		if($this->result == false) return false;
		
		$index = 0;
		$dataArray = array();
		while($row = $this->result->fetch_assoc()) {
			$dataArray[$index++] = $row;
		}
		$this->result->free();
		$this->result = false;
		
		return $dataArray;
	}
	
	//selects the values of the first column only
	function getResultArray() {
		if($this->result == false) return false;
		
		$dataArray = array();
		while($rows = $this->result->fetch_array(MYSQLI_NUM)) {
			array_push($dataArray, $rows[0]);
		}
		$this->result->free();
		$this->result = false;
		
		return $dataArray;
	}
	
	function escapeString($string) {
		return $this->connection->real_escape_string($string);
	}
}
?>