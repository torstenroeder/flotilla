<?php

class Field_MultiSelect extends Field {
	
	public $Options = array();
	
	// CONSTRUCTORS --------------------------------------------------------------
	
	protected function __construct ($Creator, $name, $size = AUTOSIZE, $required, $default_option) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->size = $size;
			$this->required = $required;
			$this->user_value = $default_option;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. new Multiple Select Field "'.$this->name.'" created');
		}
		else $this->Creator->debuglog->Write(DEBUG_ERROR,'. could not create new Multiple Select Field - name not specified');
	}
	
	static public function create() {
		// create ( name [, required [, default_option ]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 3: return new Field_MultiSelect ($args[0],$args[1],$args[2],NULL,NULL);
			case 4: return new Field_MultiSelect ($args[0],$args[1],$args[2],$args[3],NULL);
			case 5: return new Field_MultiSelect ($args[0],$args[1],$args[2],$args[3],$args[4]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create new Multiple Select - invalid number of arguments');
		}
	}
	
	// SELECT OPTIONS ------------------------------------------------------------
	
	public function addOption () {
		// addOption ( [ value [, title ]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 0: $this->Options[] = new MultiSelect_Option (count($this->Options),NULL); break;
			case 1: $this->Options[] = new MultiSelect_Option ($args[0],NULL); break;
			case 2: $this->Options[] = new MultiSelect_Option ($args[0],$args[1]); break;
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. . . could not create new Multiple Select Option - invalid number of arguments'); break;
		}
		$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Multiple Select Option "'.(isset($args[0])?$args[0]:'').'" created');
		return $this;
	}
	
	public function addOptionsFromTable () {
		// addOption ( table , value_column , title_column [, where_statement ] )
		$args = func_get_args();
		$options_querystring = "
			SELECT {$args[1]} AS value, {$args[2]} AS title
			FROM {$args[0]}
			".(isset($args[3])?'WHERE '.$args[3]:'')."
			ORDER BY {$args[2]}
		";
		$options_query = mysql_query($options_querystring);
		while ($option = mysql_fetch_object($options_query)) {
			$this->Options[] = new MultiSelect_Option ($option->value,$option->title);
			$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Multiple Select Option "'.$option->value.'" created');
		}
		return $this;
	}
	
	// HTML OUTPUT ---------------------------------------------------------------
	
	protected function HTMLStyle () {
		return $this->css_style ? " style=\"$this->css_style\"" : NULL;
	}
	
	public function HTMLOutput () {
		$output = NULL;
		if ($this->is_appended()) {
			$output .= "<label for=\"{$this->getId()}\" class=\"label\">$this->label</label>";
		}
		$output .= "\t\t\t<select multiple=\"multiple\"";
		$output .= ' name="'.$this->name.'[]"';
		$output .= ' id="'.$this->getId().'"';
		if ($this->size == AUTOSIZE) {
			$this->size = count($this->Options);
			if ($this->size > FLO_MULTISELECT_MAX_AUTOSIZE) {
				$this->size = FLO_MULTISELECT_MAX_AUTOSIZE;
			}
		}
		$output .= ' size="'.$this->size.'"';
		if ($this->is_not_hidden()) {
			$output .= $this->HTMLTitle();
			$output .= $this->HTMLClass();
			$output .= $this->HTMLStyle();
		}
		$output .= '>'.PHP_EOL;
		foreach ($this->Options as $option) {
			$is_selected = (isset($this->user_value) && in_array($option->getValue(),$this->user_value));
			$output .= $option->HTMLOutput($is_selected);
		} 
		$output .= "\t\t\t</select>".PHP_EOL;
		return $output;
	}
	
} // end class Field_Select

// SUBORDINATE CLASSES ---------------------------------------------------------

class MultiSelect_Option {
	
	protected $value;
	protected $title;
	
	// CONSTRUCTORS --------------------------------------------------------------
	
	public function __construct ($value,$title) {
		$this->value = $value;
		$this->title = $title;
	}
	
	// PROPERTIES ----------------------------------------------------------------
	
	public function getValue () {
		return $this->value;
	}
	
	// HTML OUTPUT ---------------------------------------------------------------
	
	public function HTMLOutput ($is_selected) {
		$output = "\t\t\t\t<option";
		if ($this->title) $output .= ' value="'.$this->value.'"';
		if ($is_selected) $output .= ' selected="selected"';
		if ($this->title) $output .= '>'.$this->title;
		else $output .='>'.$this->value;
		$output .= '</option>'.PHP_EOL;
		return $output;
	}
	
} // end class MultiSelect_Option

?>
