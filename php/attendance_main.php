<?php
require_once("parsecsv_lib.php");

class ATTENDANCE
{
	public $fileData = false;
	public $no_holidays = false;
	public $no_working_days = false;
	
	public function __construct()
	{
		$this->no_working_days = 30;
		//get number of working days here
	}
	
	public function readFile()
	{
		if($_FILES) {
			$uploadDir = "/var/www/embarc-utils/tmp/";
			$uploadFile = $uploadDir.basename($_FILES['emp_data']['name']);
			move_uploaded_file($_FILES['emp_data']['tmp_name'], $uploadFile);
		}
		$file = $uploadFile;
		
		$csv = new parseCSV();
		$csv->fields = array("emp_no", "date_in", "time_in", "date_out", "time_out", "job_no", "dept_no", "comment");
		$csv->parse($file);
		$this->fileData = $csv->data;
		return $this->fileData;
	}
	
	public function getInsOuts($employee_no)
	{
		$ins = array();
		$outs = array();
		
		foreach($this->fileData as $row) {
			if($row["emp_no"] == $employee_no) {
				//Time ins
				array_push($ins, array("date"=>DateTime::createFromFormat('n/j/Y', $row["date_in"])->format('U'),
										"time"=>DateTime::createFromFormat('H:i:s', $row["time_in"])->format('U')));

				//Time outs
				array_push($outs, array("date"=>DateTime::createFromFormat('n/j/Y', $row["date_out"])->format('U'),
										"time"=>DateTime::createFromFormat('H:i:s', $row["time_out"])->format('U')));
			}
		}
		
		return array("ins"=>$ins, "outs"=>$outs);
	}
	
	public function getAbsents($ins) {
		return ((float)$this->no_working_days - $this->getPresents($ins));
	}
	
	public function getPresents($ins) {
		$presents = array();
		foreach($ins as $day) {
			array_push($presents, $day["date"]);
		}
		
		return count(array_unique($presents));
	}
	
	public function getLateIns($ins) {
		$lateIns = array();
		foreach($ins as $in) {
			//Time at 10:10 -> 1374208800
			if($in["time"] > 1374208800) {
				array_push($lateIns, $in);
			}
		}
		
		return count($lateIns);
	}
	
	public function getLateOuts($outs) {
		$lateOuts = array();
		foreach($outs as $out) {
			//Time at 17:50 -> 1374236400
			if($out["time"] < 5456152631) {
				array_push($lateOuts, $out);
			}
		}
		
		return count($lateOuts);
	}
	
	public function getEmployees()
	{
		$employees = array();
		foreach($this->fileData as $row) {
			array_push($employees, $row["emp_no"]);
		}
		
		return (array_unique($employees));
	}
	
	public function getSummary($employee_no) {
		$this->readFile();
		$insouts = $this->getInsOuts($employee_no);
		echo "Absents: ".$this->getAbsents($insouts["ins"])."<br>";
		echo "Presents: ".$this->getPresents($insouts["ins"])."<br>";
		echo "Late Ins: ".$this->getLateIns($insouts["ins"])."<br>";
		echo "Late Outs: ".$this->getLateOuts($insouts["outs"])."<br>";
	}
}
?>