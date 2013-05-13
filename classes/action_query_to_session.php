<?php

class Action_Query_to_Session extends Action {
	
	protected $querystring;
	
	protected function __construct ($Creator,$querystring) {
		$args = func_get_args();
		$this->Creator = $Creator;
		$this->querystring = $querystring;
		$this->Creator->debuglog->Write(DEBUG_INFO,'. QUERY TO SESSION ACTION created');
	}
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Action_Query_to_Session ($args[0],$args[1]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. QUERY TO SESSION ACTION - invalid number of arguments');
		}
	}
	
	function Query () {
		$this->querystring = preg_replace('/(\{(\w*)\})/e',"\$this->Creator->Fields['$2']->user_value",$this->querystring);
		$result = mysql_query($this->querystring);
		if (!$result) $this->Creator->debuglog->Write(DEBUG_ERROR,$this->querystring);
		else $this->Creator->debuglog->Write(DEBUG_INFO,$this->querystring);
		return mysql_fetch_array($result,MYSQL_ASSOC);
	}
	
	function onSubmit () {
		$this->Creator->debuglog->Write(DEBUG_INFO,'. QUERY TO SESSION ACTION - writing to session');
		if (!isset($_SESSION[$this->Creator->name])) {
			$_SESSION[$this->Creator->name] = array(); 
		}
		//print_r($this->Query());
		$_SESSION[$this->Creator->name] = $this->Query();
	}
	
}

?>
