<?php

class Field_Password extends Field {
	
	// CONSTRUCTORS -----------------------------------------------------------
	
	protected function __construct ($Creator, $name, $length, $required) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->length = $length;
			$this->required = $required;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. PASSWORD FIELD "'.$this->name.'" created');
		}
		else $this->Creator->debuglog->Write(DEBUG_ERROR,'. PASSWORD FIELD - name not specified');
	}
	
	static public function create() {
		// create ( name [, length [, required [, default_value ]]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Field_Password ($args[0],$args[1],NULL,NULL);
			case 3: return new Field_Password ($args[0],$args[1],$args[2],NULL);
			case 4: return new Field_Password ($args[0],$args[1],$args[2],$args[3]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. PASSWORD FIELD - invalid number of arguments');
		}
	}
	
	// HTML OUTPUT ------------------------------------------------------------
	
	public function HTMLOutput () {
		$output = NULL;
		$output .= "\t\t\t<input";
		$output .= " name=\"$this->name\"";
		$output .= " id=\"{$this->getId()}\"";
		$output .= ' type="password"';
		if ($this->length) $output .= " maxlength=\"$this->length\" size=\"$this->length\"";
		$output .= ' value="'.htmlspecialchars(stripslashes($this->user_value)).'"';
		if ($this->is_not_hidden()) {
			$output .= $this->HTMLTitle();
			$output .= $this->HTMLClass();
			$output .= $this->HTMLStyle();
		}
		$output .= '/>'.PHP_EOL;
		return $output;
	}
	
} // end class Field_Password

?>
