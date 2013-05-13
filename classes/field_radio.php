<?php

class Field_Radio extends Field {
	
	public $RadioButtons = array();
	
	// CONSTRUCTORS -----------------------------------------------------------
	
	protected function __construct ($Creator, $name, $required, $default, $alignment = AUTO_ALIGN) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->required = $required;
			$this->user_value = $default;
			$this->alignment = $alignment;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. new Radio Field "'.$this->name.'" created');
		}
		else $this->Creator->debuglog->Write(DEBUG_ERROR,'. could not create new Radio Field - name not specified');
	}
	
	static public function create() {
		// create ( name [, required [, default ]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Field_Radio ($args[0],$args[1],NULL,NULL);
			case 3: return new Field_Radio ($args[0],$args[1],$args[2],NULL);
			case 4: return new Field_Radio ($args[0],$args[1],$args[2],$args[3]);
			case 5: return new Field_Radio ($args[0],$args[1],$args[2],$args[3],$args[4]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create new Radio Field - invalid number of arguments');
		}
	}
	
	// RADIO BUTTONS ----------------------------------------------------------
	
	public function addRadioButton () {
		// addRadioButton ( [ value [, title ]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 0: $this->RadioButtons[] = new RadioButton (count($this->RadioButtons),NULL); break;
			case 1: $this->RadioButtons[] = new RadioButton ($args[0],NULL); break;
			case 2: $this->RadioButtons[] = new RadioButton ($args[0],$args[1]); break;
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. . could not create new Radio Button - invalid number of arguments'); break;
		}
		$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Radio Button "'.$args[0].'" created');
		return $this;
	}
	
	public function setAlignment () {
		// setAlignment ( AUTO_ALIGN|VERTICAL|HORIZONTAL )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: $this->alignment = $args[0]; break;
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not set Radio Button Alignment - invalid number of arguments'); break;
		}
		return $this;
	}
	
	// HTML OUTPUT ------------------------------------------------------------
	
	protected function HTMLStyle () {
		return $this->css_style ? " style=\"$this->css_style\"" : NULL;
	}
	
	public function HTMLOutput () {
		$output = NULL;
		// AUTO ALIGN
		if ($this->alignment == AUTO_ALIGN) $this->alignment = (count($this->RadioButtons) > 2) ? VERTICAL : HORIZONTAL;
		// RADIO BUTTONS
		foreach ($this->RadioButtons as $number => $button) {
			$output .= "\t\t\t<input";
			$output .= ' type="radio" name="'.$this->name.'" id="'.$this->getId().'-'.$number.'"';
			$output .= ' value="'.$button->value.'"';
			if (isset($this->user_value) && $button->value==$this->user_value) $output .= ' checked="checked"';
			if ($this->is_not_hidden()) {
				$output .= $this->HTMLTitle();
				$output .= $this->HTMLClass();
				$output .= $this->HTMLStyle();
			}
			$label = $button->title?$button->title:$button->value;
			// no space between button and label
			$output .= '>'.'<label for="'.$this->getId().'-'.$number.'">'.$label.'</label>';
			if ($this->alignment == VERTICAL) $output .= '<br/>';
			$output .= PHP_EOL;
		}
		return $output;
	}
	
} // end class Field_Radio

// SUBORDINATE CLASSES --------------------------------------------------------
	
class RadioButton {
	
	public $value;
	public $title;
	
	public function __construct ($value,$title) {
		$this->value = $value;
		$this->title = $title;
	}
	
} // end class RadioButton

?>
