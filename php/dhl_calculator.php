<?php
require_once("mysql_interface.php");
require_once("settings.php");

class DHL_CALCULATOR
{
	public $account = false;
	public $mInterface;
	public $settingsObj;
	public $settings;
	
	public function __construct() {
		$this->mInterface = new MYSQL_INTERFACE();
		$this->settingsObj = new SETTINGS();
		$this->settings = $this->settingsObj->getConstants();
	}
	
	public function getOptimalCost($country, $type, $weight) {
		$weight = (float) $weight;
		
		$prices = array();
		
		$requestedPrice = $this->calculate($country, $type, $weight);
		array_push($prices, $requestedPrice);
		
		//additional price calculation for cheaper costs, only if requested price is not multiplied		
		if(!$requestedPrice["multiplied"]) array_push($prices, $this->calculate($country, $type, null));
		
		return $prices;
	}
	
	public function calculate($country, $type, $weight) {
		$accounts = $this->mInterface->getAllAccounts();
		$prices = array();
		
		for($i=0; $i<count($accounts); $i++) {
			$multiplier = false;
			$accountNo = $accounts[$i]["accountNo"];
			
			//if weight is not specified, assume weight to be equal to Maximum weight of that type in that account
			if(!$weight) $weight = (float) $this->settings["MAX_".$type."_".$accountNo];
			
				switch($type) {
					case "DOC":
						if($weight > $this->settings["MAX_DOC_".$accountNo])
							$multiplier = true;
						break;
					case "NDOC":
						if($weight >= $this->settings["MAX_NDOC_".$accountNo])
							$multiplier = true;
						break;
				}
				
				$zone = $this->mInterface->getZone($country);				
				$zone = $zone[0][$accountNo];
								
				if($multiplier == true) {
					$multipliedPrice = $this->mInterface->getDHLPrice($accountNo, $zone, $type."MUL", $weight);
					
					if($multipliedPrice != "") {
						$multipliedPrice = (float) $multipliedPrice[0];
						$result = $multipliedPrice*$weight;
						array_push($prices, array("account"=>$accountNo, "price"=>$result, "type"=>$type."MUL", "multiplied"=>true));
					}
				} else {
					$result = $this->mInterface->getDHLPrice($accountNo, $zone, $type, $weight);
					
					if($result != "") {
						$result = (float) $result[0];
						array_push($prices, array("account"=>$accountNo, "price"=>$result, "type"=>$type, "multiplied"=>false));
					}
				}
		}

		$bestPrice = array("type"=>"", "price"=>INF, "account"=>"");
		foreach($prices as $price) {
			if($price["price"] < $bestPrice["price"]) {
				$bestPrice["price"] = $price["price"];
				$bestPrice["account"] = $price["account"];
				$bestPrice["type"] = $price["type"];
				$bestPrice["weight"] = $weight;
				$bestPrice["multiplied"] = $price["multiplied"];
			}
		}
		return $bestPrice;
	}
}
?>
