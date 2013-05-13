<?php

class Field_Protected extends Field {
	
	// data interaction:
	// - default value can be overridden by database connection
	
	// CONSTRUCTORS -----------------------------------------------------------
	
	protected function __construct ($Creator, $name, $default_value) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->user_value = $default_value;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. new Protected Field "'.$this->name.'" created');
		}
		else $this->Creator->debuglog->Write(DEBUG_ERROR,'. could not create new Protected Field - name not specified');
	}
	
	static public function create() {
		// create ( name [, value ] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Field_Protected ($args[0],$args[1],NULL);
			case 3: return new Field_Protected ($args[0],$args[1],$args[2]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create new Protected Field - invalid number of arguments');
		}
	}
	
	// HTML OUTPUT ------------------------------------------------------------
	
	public function HTMLOutput () {
		$output = NULL;
		$output .= '<input';
		$output .= " name=\"$this->name\"";
		$output .= " id=\"field-$this->name\"";
		$output .= ' type="text"';
		$output .= ' readonly="readonly"';
		$output .= " value=\"".htmlspecialchars(stripslashes($this->user_value))."\"";
		$output .= '/>'.PHP_EOL;
		return $output;
	}
	
} // end class Field_Protected

?>
