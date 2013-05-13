<?php

class Field_Checkbox extends Field {
	
	protected $default_value = 1;
	
	// CONSTRUCTORS --------------------------------------------------------------
	
	protected function __construct ($Creator, $name, $value, $required, $checked) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->required = $required;
			if ($value) $this->default_value = $value;
			if ($checked) $this->user_value = $value;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. new Checkbox "'.$this->name.'" created');
		}
		else $this->Creator->debuglog->Write(DEBUG_ERROR,'. could not create Checkbox - name not specified');
	}
	
	static public function create() {
		// create ( Creator, name [, value [, required [, checked ]]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Field_Checkbox ($args[0],$args[1],NULL,NULL,NULL);
			case 3: return new Field_Checkbox ($args[0],$args[1],$args[2],NULL,NULL);
			case 4: return new Field_Checkbox ($args[0],$args[1],$args[2],$args[3],NULL);
			case 5: return new Field_Checkbox ($args[0],$args[1],$args[2],$args[3],$args[4]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create Checkbox - invalid number of arguments');
		}
	}
	
	// HTML OUTPUT ---------------------------------------------------------------
	
	protected function HTMLStyle () {
		return $this->css_style ? ' style="'.$this->css_style.'"' : NULL;
	}
	
	public function HTMLOutput () {
		$output = '';
		$output .= "\t\t\t<input";
		$output .= ' name="'.$this->name.'"';
		$output .= ' id="'.$this->getId().'"';
		$output .= ' type="checkbox"';
		$output .= ' value="'.htmlspecialchars(stripslashes($this->default_value)).'"';
		if ($this->is_not_hidden()) {
			$output .= $this->HTMLTitle();
			$output .= $this->HTMLClass();
			$output .= $this->HTMLStyle();
		}
		if ($this->user_value) {
			$output .= ' checked="checked"';
		}
		$output .= '/>'.PHP_EOL;
		return $output;
	}
	
} // end class Field_Checkbox

?>
