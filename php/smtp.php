<?php
require_once("mysql_interface.php");
require_once("users.php");
require "PHPMailer/PHPMailerAutoload.php";

class SMTPs
{
	public $mInterface;
	public $users;
	public $LOG_FILE = "/var/www/embarc-utils/logs/mails.log";
	
	public function __construct() {
		$this->mInterface = new MYSQL_INTERFACE();
		$this->users = new USERS();
		
		// clear the .log file initially
		file_put_contents($this->LOG_FILE, "", FILE_APPEND | LOCK_EX);
	}
	
	// test a specified SMTP server
	public function testSMTPDetails($postData) {
		$username = $_SESSION['user'];
		
		// details of user, to send email to
		$userDetails = $this->users->getUser($username);
		
		// data received from client
		$settings = array("host"=>$postData['host'], "port"=>$postData['port'], "username"=>$postData['username'], "password"=>$postData['password'], "enAuth"=>($postData['enAuth'] == 1?true:false), "enSSL"=>($postData['enSSL'] == 1?true:false));
		
		$result = $this->sendMail($settings, $userDetails, "Congrats! SMTP server works", "This is a test mail from ".$settings['host'].". If you've received this mail, the SMTP server settings are correct.", 2);
		
		if($result == "SUCCESS") return "Mail sent successfully.";
	}
	
	public function sendMail($settings, $recipients, $subject="embarc-utils", $body, $debugMode=0) {
		global $SM_FROM, $SM_REPLYTO;
		
		if(!$settings) $settings = $this->_defaultSMTPSettings();
		
		// start PHP mailer setup
		$mail = new PHPMailer;
		$mail->isSMTP();															// Set mailer to use SMTP
		$mail->SMTPDebug = $debugMode;												// Debug mode - Commands + Data
		$mail->Debugoutput = "html";												// HTML style debug output
		$mail->Host = $settings['host'];											// SMTP server
		$mail->Port = $settings['port'];											// SMTP port [Gmail - (ssl: 465/25, tls: 587)]
		$mail->SMTPAuth = $settings['enAuth'];										// Enable SMTP authentication
		if($settings['enSSL']) $mail->SMTPSecure = 'ssl';							// Enable encryption
		$mail->SMTPKeepAlive = false;												// SMTP connection will close after sending the email
		$mail->Username = $settings['username'];									// SMTP username
		$mail->Password = $settings['password'];									// SMTP password
		$mail->From = $SM_FROM['email'];											// Email ID of sender
		$mail->FromName = $SM_FROM['name'];											// Name of sender
		
		// Adding recipients
		$emails = array();
		for($i=0; $i<count($recipients); $i++) {
			array_push($emails, $recipients[$i]['email']);
			$mail->addAddress($recipients[$i]['email'], $recipients[$i]['name']);	// Details of receipent
		}
		
		$mail->addReplyTo($SM_REPLYTO['email'], $SM_REPLYTO['name']);				// Add reply-to address
		$mail->WordWrap = 50;														// Set word wrap to 50 characters
		$mail->isHTML(true);														// Set email format to HTML
		
		// the mail
		$mail->Subject = $subject;
		$mail->Body = $body;
		
		// sending email
		if(!$mail->send()) {
			$this->do_log($mail->ErrorInfo);
			return $mail->ErrorInfo;
		}
		
		$this->do_log("Email dispatched to " . implode(" ", $emails));
		return "SUCCESS";
	}
	
	public function _defaultSMTPSettings() {
		global $SM_HOST, $SM_PORT, $SM_USERNAME, $SM_PASSWORD, $SM_AUTH, $SM_SSL;
		
		return array("host"=>$SM_HOST, "port"=>$SM_PORT, "username"=>$SM_USERNAME, "password"=>$SM_PASSWORD, "enAuth"=>$SM_AUTH, "enSSL"=>$SM_SSL);
	}
	
	public function do_log($data) {
		file_put_contents($this->LOG_FILE, date("r")." - ".$data."\r\n", FILE_APPEND | LOCK_EX);
	}
}
?>