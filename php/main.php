<?php
session_start();

// required files
require_once "mysql_interface.php";
require_once "login.php";
require_once "users.php";
require_once "alerts_reminders.php";
require_once "smtp.php";

// default configuration
date_default_timezone_set ( 'UTC' );

switch ($_GET['util']) {
	case "courier":
		courier($_GET['fx']);
		break;
		
	case "inventory":
		inventory($_GET['fx']);
		break;
		
	case "attendance":
		attendance($_GET['fx']);
		break;
	
	case "login":
		login($_GET['fx']);
		break;
		
	case "servers":
		server($_GET['fx']);
		break;
		
	case "misc":
		misc($_GET['fx']);
		break;
		
	case "user":
		user($_GET['fx']);
		break;
		
	case "anr":
		anr($_GET['fx']);
		break;
		
	case "smtp":
		smtp($_GET['fx']);
		break;
}

function login($fx)
{
	$login = new LOGIN();
	
	switch ($fx)
	{
		case "login":
			echo $login->authenticate($_POST['username'], $_POST['hash'], $_POST['remember']);
			break;
			
		case "logout":
			$login->logout();
			header("Location: /embarc-utils/login.html");
			break;
	}
}

function courier($fx)
{
	switch ($fx)
	{
		case "getCountries":
			$mInterface = new MYSQL_INTERFACE();
			echo $mInterface->getCountries();
			break;
			
		case "dhlCalculate":
			require_once('dhl_calculator.php');
			$calculator = new DHL_CALCULATOR();
			
			if(!isset($_POST['country']) || !isset($_POST['type']) || !isset($_POST['weight'])) return "";
			echo json_encode($calculator->getOptimalCost($_POST['country'], $_POST['type'], $_POST['weight']));
			break;
			
		case "setSettings":
			require_once('settings.php');
			$settings = new SETTINGS();
			echo $settings->setCSSettings($_POST['pref']);
			break;
			
		case "getSettings":
			require_once('settings.php');
			$settings = new SETTINGS();
			echo $settings->getCSSettings();
			break;
	}
}

function attendance($fx)
{
	switch ($fx)
	{
		case "attendanceCalculate":
			require_once('attendance_main.php');
			$attend = new ATTENDANCE();
			
			$attend->getSummary(21);
			break;
	}
}

function inventory($fx)
{
	require_once("inventory.php");
	$inventory = new STOCK();
	switch($fx)
	{
		case "getTrackers":
			echo json_encode($inventory->getTrackersList());
			break;
			
		case "saveStockItem":
			echo $inventory->saveItem($_POST);
			break;
			
		case "updateStockItem":
			echo $inventory->updateItem($_POST);
			break;
			
		case "getItemsInStock":
			echo json_encode($inventory->getItemsInStock($_GET["prop"]));
			break;
			
		case "getItemInStock":
			$item = $inventory->getItemInStock($_GET["prop"], $_GET["val"]);
			if(gettype($item) == "string") echo $item;
			else echo json_encode($item);
			break;
			
		case "getClients":
			echo json_encode($inventory->getClients());
			break;
			
		case "search":
			echo json_encode($inventory->findItems($_POST));
			break;
			
		case "saveClient":
			echo $inventory->saveClient($_POST);
			break;
			
		case "saveTracker":
			echo $inventory->saveTracker($_POST);
			break;
	}
}

function misc($fx)
{
	require_once("misc.php");
	$misc = new MISC();
	
	switch($fx)
	{
		case "getModules":
			$modules = $misc->getModules();
			if(gettype($modules) == "string") echo $modules;
			else echo json_encode($modules);
			break;
			
		case "listModules":
			$modules = $misc->listModules();
			if(gettype($modules) == "string") echo $modules;
			else echo json_encode($modules);
			break;
			
		case "getPreferences":
			echo json_encode($misc->getPreferences($_GET["module"]));
			break;
			
		case "savePreferences":
			echo $misc->savePreferences($_GET["module"], $_POST);
			break;
	}
}

function server($fx)
{
	require_once("servers.php");
	$servers = new SERVER();
	
	switch($fx)
	{
		case "add":
			if(isset($_GET['id'])) $id = $_GET['id'];
			else $id = false;
			
			echo $servers->addServer($_POST, $id);
			break;
			
		case "get":
			echo json_encode($servers->getServer($_GET['id']));
			break;
			
		case "delete":
			echo $servers->removeServer($_GET['id']);
			break;
			
		case "list":
			echo json_encode($servers->getServersList());
			break;
			
		case "status":
			echo $servers->getStatus($_GET['ip'], $_GET['tcInt']);
			break;
			
		case "checkSecret":
			echo $servers->verify_AES_passphrase($_POST["hash"]);
			break;
			
		case "addDatacentre":
			echo $servers->addDC($_POST);
			break;
			
		case "getDatacentres":
			echo json_encode($servers->getDC());
			break;
	}
}

function user($fx)
{
	$users = new USERS();
	
	switch ($fx)
	{
		case "add":
			if(isset($_GET['username'])) $username = $_GET['username'];
			else $username = false;
			
			echo $users->saveUser($_POST, $username);
			break;
			
		case "list":
			echo json_encode($users->getUsersList());
			break;
			
		case "remove":
			echo $users->remove($_GET['username']);
			break;
			
		case "get":
			echo json_encode($users->getUser($_GET['username']));
			break;
	}
}

function anr($fx)
{
	$al_rem = new AL_REM();
	
	switch($fx)
	{
		case "getMAR":
			$list = $al_rem->get_modules_alerts_reminders();
			if(gettype($list) == "string") echo $list;
			else echo json_encode($list);
			break;
			
		case "save":
			if(isset($_GET['id'])) $id = $_GET['id'];
			else $id = false;
		
			echo $al_rem->save($_POST, $id);
			break;
			
		case "get";
			$schedule = $al_rem->get();
			if(gettype($schedule) == "string") echo $schedule;
			else echo json_encode($schedule);
			break;
	}
}

function smtp($fx)
{
	$smtp = new SMTPs();
	
	switch($fx)
	{
		case "sendMail":
			echo $smtp->testSMTPDetails($_POST);
			break;
	}
}
?>