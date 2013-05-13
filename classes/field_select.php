<?php

class Field_Select extends Field {
	
	public $Options = array();
	
	// CONSTRUCTORS --------------------------------------------------------------
	
	protected function __construct ($Creator, $name, $required, $default_option) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->required = $required;
			$this->user_value = $default_option;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. new Select Field "'.$this->name.'" created');
		}
		else $this->Creator->debuglog->Write(DEBUG_ERROR,'. could not create new Select Field - name not specified');
	}
	
	static public function create() {
		// create ( name [, required [, default_option ]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Field_Select ($args[0],$args[1],NULL,NULL);
			case 3: return new Field_Select ($args[0],$args[1],$args[2],NULL);
			case 4: return new Field_Select ($args[0],$args[1],$args[2],$args[3]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create new Select Field - invalid number of arguments');
		}
	}
	
	// SELECT OPTIONS ------------------------------------------------------------
	
	public function addOption () {
		// addOption ( [ value [, title ]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 0: $this->Options[] = new Select_Option (count($this->Options),NULL); break;
			case 1: $this->Options[] = new Select_Option ($args[0],NULL); break;
			case 2: $this->Options[] = new Select_Option ($args[0],$args[1]); break;
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. . . could not create new Select Option - invalid number of arguments'); break;
		}
		$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Select Option "'.(isset($args[0])?$args[0]:'').'" created');
		return $this;
	}
	
	public function addOptionsFromTable () {
		// addOption ( table , value_column , title_column [, where_statement [, db_connection]] )
		// example: addOption ('contacts','contact_id','contact_name','`contact_name` LIKE 'A*', $db_connection);
		
		// remark: if you use the 5th parameter, a database connection must be declared before
		// example: $db_connection = mysql_connect('example.com', 'mysql_user', 'mysql_password');
		
		$args = func_get_args();
		$options_querystring = "
			SELECT {$args[1]} AS value, {$args[2]} AS title
			FROM {$args[0]}
			".(isset($args[3])?'WHERE '.$args[3]:'')."
			ORDER BY {$args[2]}
		";
		if (isset($args[4])) {
			$options_query = mysql_query($options_querystring,$args[4]);
			mysql_select_db($this->Creator->Connection->link);
		}
		else {
			$options_query = mysql_query($options_querystring);
		}
		while ($option = mysql_fetch_object($options_query)) {
			$this->Options[] = new Select_Option ($option->value,$option->title);
			$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Select Option "'.$option->value.'" created');
		}
		return $this;
	}
	
	public function addOptionsFromArray ( $array ) {
		// addOption ( array )
		foreach ($array as $key => $value) {
			$this->Options[] = new Select_Option ($key,$value);
			$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Select Option "'.$key.'" created');
		}
		return $this;
	}
	
	public function addOptionsFromXML () {
		// addOption ( Resource-URL , namespace, division , value , title)
		$args = func_get_args();
		$feed = simplexml_load_file($args[0]);
		
		$xml =  $feed->children($args[1]);
		
		foreach ($xml->$args[2] as $entries) {
			$child = $entries->children($args[1]);
			$this->Options[] = new Select_Option ($child->$args[3],$child->$args[4]);
			$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Select Option "'.$child->id.'" created');
		}
		return $this;
		// written by Stefan Dumont
	} 
	
	public function addOptionsFromRange () {
		// addOption ( [to]|[from, to] [, skip [, prefix [, suffix ]]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1:
				for ($i = 1; $i <= $args[0]; $i++) {
					$this->Options[] = new Select_Option ($i, $i);
					$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Select Option "'.$i.'" created');
				}
				break;
			case 2:
				for ($i = $args[0]; $i <= $args[1]; $i++) {
					$this->Options[] = new Select_Option ($i, $i);
					$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Select Option "'.$i.'" created');
				}
				break;
			case 3:
				for ($i = $args[0]; $i <= $args[1]; $i+=$args[2]) {
					$this->Options[] = new Select_Option ($i, $i);
					$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Select Option "'.$i.'" created');
				}
				break;
			case 4:
				for ($i = $args[0]; $i <= $args[1]; $i+=$args[2]) {
					$this->Options[] = new Select_Option ($i, $args[3].$i);
					$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Select Option "'.$args[3].$i.'" created');
				}
				break;
			case 5:
				for ($i = $args[0]; $i <= $args[1]; $i+=$args[2]) {
					$this->Options[] = new Select_Option ($i, $args[3].$i.$args[4]);
					$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Select Option "'.$args[3].$i.$args[4].'" created');
				}
				break;
		}
		return $this;
	}
	
	// HTML OUTPUT ---------------------------------------------------------------
	
	protected function HTMLStyle () {
		return $this->css_style ? " style=\"$this->css_style\"" : NULL;
	}
	
	public function HTMLOutput () {
		$output = NULL;
		$output .= "\t\t\t<select";
		$output .= ' name="'.$this->name.'"';
		$output .= ' id="'.$this->getId().'"';
		if ($this->is_not_hidden()) {
			$output .= $this->HTMLTitle();
			$output .= $this->HTMLClass();
			$output .= $this->HTMLStyle();
		}
		$output .= '>'.PHP_EOL;
		// SELECT OPTIONS
		foreach ($this->Options as $option) {
			$is_selected = (isset($this->user_value) && $option->getValue()==$this->user_value);
			$output .= $option->HTMLOutput($is_selected);
		}
		$output .= "\t\t\t</select>".PHP_EOL;
		return $output;
	}
	
} // end class Field_Select

// SUBORDINATE CLASSES ---------------------------------------------------------

class Select_Option {
	
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
	
} // end class Select_Option

?>
