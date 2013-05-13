<?php

class Field_Hidden extends Field {
	
	// data interaction:
	// - fixed default value, cannot be overridden by database connection
	// - can post a value
	
	// CONSTRUCTORS --------------------------------------------------------------
	
	protected function __construct ($Creator, $name, $value) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->user_value = $value;
			$this->htmltype = 'hidden';
			$this->Creator->debuglog->Write(DEBUG_INFO,'. new Hidden Field "'.$this->name.'" created');
		}
		else $this->Creator->debuglog->Write(DEBUG_ERROR,'. could not create Hidden Field - name not specified');
	}
	
	static public function create() {
		// create ( name [, value ] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Field_Hidden ($args[0],$args[1],NULL);
			case 3: return new Field_Hidden ($args[0],$args[1],$args[2]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create Hidden Field - invalid number of arguments');
		}
	}
	
	public function HTMLOutput () {
		$output = NULL;
		$output .= '<input';
		$output .= " name=\"$this->name\"";
		$output .= " id=\"field-$this->name\"";
		$output .= " type=\"hidden\"";
		$output .= " value=\"".htmlspecialchars(stripslashes($this->user_value))."\"";
		$output .= '/>'.PHP_EOL;
		return $output;
	}
	
	protected function hide () {
		$this->htmltype = 'hidden';
	}
	
	protected function show () {
		$this->htmltype = 'text';
	}
	
} // end class Field_Hidden

?>
