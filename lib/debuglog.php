<?php

// DEBUG LEVELS
define ('DEBUG_ERROR',1);
define ('DEBUG_WARNING',2);
define ('DEBUG_NOTICE',3);
define ('DEBUG_INFO',4);

class DebugLog {
	
	protected $log = array();
	protected $log_level = 3;
	protected $timer;

	public function __construct ($log_level) {
		// DEBUG LEVEL
		$this->log_level = $log_level;
		// TIMER
		require_once 'timer.php';
		$this->timer = new Timer(3);
		$this->timer->Start();
	}
	
	public function Write ($priority,$message) {
		if ($this->log_level >= $priority) $this->log[] = array ($this->timer->GetCurrentTime(3),$priority,$message);
	}
	
	public function Show () {
		$output = NULL;
		if ($this->log_level > 0) {
			$output .= '<pre>'.PHP_EOL;
			$output .= '--- DEBUG LOG ---'.PHP_EOL;
			//print_r($this->log);
			foreach ($this->log as $entry) {
				// time
				$output .= sprintf('%07.3f',$entry[0]);
				// level
				switch ($entry[1]) {
					case DEBUG_NOTICE:
						$output .= ' NOTICE:';
						break; 
					case DEBUG_WARNING:
						$output .= ' WARNING:';
						break; 
					case DEBUG_ERROR:
						$output .= ' ERROR:';
						break;
				}
				// message
				$output .= ' '.$entry[2].PHP_EOL;
			}
			$output .= '--- END OF DEBUG LOG ---'.PHP_EOL;
			$output .= '</pre>';
		}
		return $output;
	}

}

?>
