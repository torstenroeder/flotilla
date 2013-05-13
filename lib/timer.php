<?php

class Timer {

	protected $start = NULL;
	protected $stop = NULL;
	protected $lap = NULL;
	protected $last_lap = NULL;
	protected $exponent = 3;
	
	function __construct ($exponent) {
		$this->exponent = $exponent;
	}
	
	function Calc ($value) {
		$result = round($value*pow(10,$this->exponent),$this->exponent);
		return $result;
	}
	
	function GetMicroTime() { 
		list($usec, $sec) = explode(' ',microtime()); 
		return ((float)$usec + (float)$sec); 
	}
	
	function Start() {
		$this->start = $this->GetMicroTime();
		$this->last_lap = $this->start;
	}

	function Stop() {
		$this->stop = $this->GetMicroTime();
	}
	
	function GetCurrentTime() {
		$result = $this->Calc($this->GetMicroTime() - $this->start);
		return $result;
	}

	function GetLapTime() {
		$this->lap = $this->GetMicroTime();
		$result = $this->Calc($this->lap - $this->last_lap);
		$this->last_lap = $this->lap;
		return $result;
	}

	function GetTotalTime() {
		$result = $this->Calc($this->stop - $this->start);
		return $result;
	}

}

?>
