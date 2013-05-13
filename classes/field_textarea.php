<?php

class Field_TextArea extends Field {
	
	// CONSTRUCTORS --------------------------------------------------------------
	
	protected function __construct ($Creator, $name, $rows = FLO_TEXTAREA_ROWS, $required = OPTIONAL, $default_text = NO_DEFAULT) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->rows = $rows;
			$this->required = $required;
			$this->user_value = $default_text;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. new TextArea Field "'.$this->name.'" created');
		}
		else $this->Creator->debuglog->Write(DEBUG_ERROR,'. could not create new TextArea Field - name not specified');
	}
	
	static public function create() {
		// create ( textarea_name [, rows [, required [, default_text ]]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Field_TextArea ($args[0],$args[1]);
			case 3: return new Field_TextArea ($args[0],$args[1],$args[2]);
			case 4: return new Field_TextArea ($args[0],$args[1],$args[2],$args[3]);
			case 5: return new Field_TextArea ($args[0],$args[1],$args[2],$args[3],$args[4]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create new TextArea Field - invalid number of arguments'); break;
		}
	}
	
	// HTML OUTPUT ---------------------------------------------------------------
	
	protected function HTMLStyle () {
		$style = NULL;
		// AUTO WIDTH goes first, so it can be overridden by user style
		if (FLO_AUTO_FIELD_WIDTH) {
			$auto_width = FLO_AUTO_FIELD_WIDTH_MAX;
			$style .= "width:{$auto_width}px";
		}
		if ($this->css_style) {
			if (isset($style)) $style .= '; ';
			$style .= $this->css_style;
		}
		return (isset($style) ? ' style="'.$style.'"' : NULL);
	}
	
	public function HTMLOutput () {
		$output = NULL;
		$output .= "\t\t\t<textarea";
		$output .= ' name="'.$this->name.'"';
		$output .= ' id="'.$this->getId().'"';
		$output .= ' rows="'.$this->rows.'"';
		$output .= $this->HTMLTitle();
		$output .= $this->HTMLClass();
		$output .= $this->HTMLStyle();
		$output .= '>';
		$output .= htmlspecialchars(stripslashes($this->user_value));
		$output .= '</textarea>'.PHP_EOL;
		return $output;
	}
} // end class Field_TextArea

?>
