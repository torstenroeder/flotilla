<?php

class Action_Session extends Action {
	
	protected $vars;
	
	protected function __construct ($Creator,$vars) {
		$args = func_get_args();
		$this->Creator = $Creator;
		$this->vars = $vars;
		$this->Creator->debuglog->Write(DEBUG_INFO,'. SESSION ACTION created');
	}
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new Action_Session ($args[0],NULL);
			case 2: return new Action_Session ($args[0],$args[1]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. SESSION ACTION - invalid number of arguments');
		}
	}
	
	function onLoad () {
		if ($_SESSION[$this->name]) {
			DebugInfo ('Action_Session: reading from session');
			reset($_SESSION[$this->name]);
			while (list($field_name, $field_value) = each($_SESSION[$this->name])) {
				if (isset($this->Fields[$field_name])) {
					$this->Fields[$field_name]->user_value = $field_value;
					$this->Creator->debuglog->Write(DEBUG_INFO,"SESSION ACTION - reading ... $field_name => $field_value");
				}
			}
		}
		else {
			$this->Creator->debuglog->Write(DEBUG_INFO,'SESSION ACTION - no session data found');
		}
	}
	
	function onSubmit () {
		$this->Creator->debuglog->Write(DEBUG_INFO,'SESSION ACTION - writing to session');
		if (!isset($_SESSION[$this->Creator->name])) {
			$_SESSION[$this->Creator->name] = array(); 
		}
		reset($this->Creator->Fields);
		while (list($index, $Field) = each($this->Creator->Fields)) {
			$_SESSION[$this->Creator->name][$Field->name] = $Field->user_value;
			$this->Creator->debuglog->Write(DEBUG_INFO,"SESSION ACTION - writing ... {$_SESSION[$this->Creator->name][$Field->name]} => $Field->user_value");
		}
	}
	
}

?>
