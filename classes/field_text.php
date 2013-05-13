<?php

class Field_Text extends Field {
	
	public $autowidth = true;
	
	// CONSTRUCTORS -----------------------------------------------------------
	
	protected function __construct ($Creator, $name, $length = FLO_FIELD_LENGTH, $required = OPTIONAL, $default_value = NULL) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->length = $length;
			$this->required = $required;
			$this->user_value = $default_value;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. TEXT FIELD "'.$this->name.'" created');
		}
		else {
			$this->Creator->debuglog->Write(DEBUG_ERROR,'. TEXT FIELD - name not specified');
		}
	}
	
	static public function create() {
		// create ( Creator, name [, length [, required [, default_value ]]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Field_Text ($args[0],$args[1]);
			case 3: return new Field_Text ($args[0],$args[1],$args[2]);
			case 4: return new Field_Text ($args[0],$args[1],$args[2],$args[3]);
			case 5: return new Field_Text ($args[0],$args[1],$args[2],$args[3],$args[4]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. TEXT FIELD - invalid number of arguments');
		}
	}
	
	public function HTMLOutput () {
		$output = NULL;
		$output .= "\t\t\t<input";
		$output .= ' name="'.$this->name.'"';
		$output .= ' id="'.$this->getId().'"';
		$output .= ' type="text"';
		if ($this->language) $output .= ' lang="'.$this->language.'"';
		if ($this->direction) $output .= ' dir="'.$this->direction.'"';
		if ($this->length) $output .= ' maxlength="'.$this->length.'" size="'.$this->length.'"';
		$output .= ' value="'.htmlspecialchars(stripslashes($this->user_value)).'"';
		if ($this->is_not_hidden()) {
			$output .= $this->HTMLTitle();
			$output .= $this->HTMLClass();
			$output .= $this->HTMLStyle();
		}
		$output .= '/>'.PHP_EOL;
		return $output;
	}
	
} // end class Field_Text

?>
