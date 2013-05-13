<?php

class FormCondition {

	public $type;
	public $error_message;
	
	private $Creator;
	private $error;
	
	protected function __construct ( $Creator, $type, $parameter, $error_message ) {
		$this->Creator = $Creator;
		$this->type = $type;
		$this->parameter = $parameter;
		$this->error_message = $error_message;
	}
	
	static public function create () {
		// addFormCondition ( Creator, type [, parameter [, error_message ]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 3: return new FormCondition ($args[0],$args[1],$args[2],FLO_FORM_CONDITION_ERROR); break;
			case 4: return new FormCondition ($args[0],$args[1],$args[2],$args[3]); break;
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'addFormCondition ignored: invalid number of arguments');
		}
	}
	
	function Evaluate () {
		$parent = &$this->parent;
		if (isset($this->parameter)) {
			switch ($this->type) {	
				case MYSQL_STATEMENT:
					$this->Creator->debuglog->Write(DEBUG_INFO,' . . testing FormCondition (MySQL statement)');
					$this->parameter = preg_replace('/(\{(\w*)\})/e',"\$this->Creator->Fields['$2']->user_value",$this->parameter);
					$query = mysql_query($this->parameter);
					if (mysql_num_rows($query)==0) {
						$this->error = $this->error_message;
						$this->Creator->debuglog->Write(DEBUG_INFO,' . . MySQL statement returned no row: '.$this->parameter);
					}
					else {
						$this->Creator->debuglog->Write(DEBUG_INFO,' . . MySQL statement returned at least one row');
					}
					break;
				case USER_STATEMENT:
					if (!call_user_func($this->parameter)) {
						$this->error = $this->error_message;
					}
					break;
			}
		} // end if
		return (is_null($this->error));
	}
} // end class Condition

?>
