<?php

class Field_StaticText extends Field {
	
	public $autowidth = false; // default is true
	
	// CONSTRUCTORS -----------------------------------------------------------
	
	protected function __construct ($Creator, $name, $default_value = NULL) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->user_value = $default_value;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. new StaticText "'.$this->name.'" created');
		}
		else {
			$this->Creator->debuglog->Write(DEBUG_ERROR,'. could not create new StaticText - name not specified');
		}
	}
	
	static public function create() {
		// create ( name [, length [, required [, default_value ]]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Field_StaticText ($args[0],$args[1]);
			case 3: return new Field_StaticText ($args[0],$args[1],$args[2]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create new StaticText - invalid number of arguments');
		}
	}
	
	public function HTMLOutput () {
		$output = NULL;
		$output .= "\t\t\t<div";
		$output .= ' name="'.$this->name.'"';
		$output .= ' id="field-'.$this->name.'"';
		if ($this->language) $output .= ' lang="'.$this->language.'"';
		if ($this->direction) $output .= ' dir="'.$this->direction.'"';
		if ($this->is_not_hidden()) {
			$output .= $this->HTMLTitle();
			$output .= $this->HTMLClass();
			$output .= $this->HTMLStyle();
		}
		$output .= '/>';
		$output .= stripslashes($this->user_value);
		$output .= '</div>'.PHP_EOL;
		return $output;
	}
	
} // end class Field_StaticText

?>
