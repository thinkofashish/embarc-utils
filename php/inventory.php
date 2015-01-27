<?php
require_once("mysql_interface.php");

class STOCK
{
	public $mInterface;
	
	public function __construct() {
		$this->mInterface = new MYSQL_INTERFACE();
	}
	
	public function getTrackersList() {
		return $this->mInterface->in_getTrackers();
	}
	
	public function saveItem($data) {
		$str = array();
		foreach($data as $key=>$value) {
			array_push($str, $key."='".$value."'");
		}
		array_push($str, "in_username='".$_SESSION['user']."'");
		array_push($str, "inStock=1");
		
		if($this->doesIMEIExists($data["imei"]) === 1) {
			return "IMEI_EXISTS";
		}
		
		if($this->mInterface->in_addStockItem(join(",", $str))) return "SUCCESS";
		else return "ERROR";
	}
	
	public function updateItem($data) {
		$str = array();
		$id = false;
		
		$id = $data["id"];
		unset($data["id"]);
		
		if($this->doesIMEIExists($data["imei"]) === 0) {
			return "IMEI_NOT_STOCK";
		}
		unset($data["imei"]);
		
		foreach($data as $key=>$value) {
			array_push($str, $key."='".$value."'");
		}
		array_push($str, "out_username='".$_SESSION['user']."'");
		array_push($str, "inStock=0");
		
		if($this->mInterface->in_updateStockItem(join(",", $str), $id)) return "SUCCESS";
		else return "ERROR";
	}
	
	public function doesIMEIExists($imei) {
		if($this->mInterface->in_getStock("imei", $imei) == null) return 0;
		else return 1;
	}
	
	public function getItemInStock($field, $value) {
		$item = $this->mInterface->in_getItemInStock($field, $value);
		if($item == null) return "ERROR";
		else return $item[0];
	}
	
	public function getClients() {
		return $this->mInterface->in_getClients();
	}
	
	public function findItems($data) {
		if($data["wholeWord"] == 1) {
			$queryPart = $data["criteria"]."='".$data["query"]."'";
		} else {
			$queryPart = $data["criteria"]." like '%".$data["query"]."%'";
		}
		
		if($data["inStock_ex"] == 1) $queryPart .= " and inStock<>1";
		if($data["outStock_ex"] == 1) $queryPart .= " and inStock<>0";
		
		$result = $this->mInterface->in_matchStock($data["count"], $queryPart);
		
		if($data["count"] == 1) {
			return $result[0];
		} else {
			return $result;
		}
	}
	
	public function saveClient($data) {
		$str = array();
		foreach($data as $key=>$value) {
			array_push($str, $key."='".$value."'");
		}
				
		if($this->mInterface->in_addClient(join(",", $str))) return "SUCCESS";
		else return "ERROR";
	}
	
	public function saveTracker($data) {
		$str = array();
		foreach($data as $key=>$value) {
			array_push($str, $key."='".$value."'");
		}
				
		if($this->mInterface->in_addTracker(join(",", $str))) return "SUCCESS";
		else return "ERROR";
	}
}
?>