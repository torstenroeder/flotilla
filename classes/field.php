<?php

abstract class Field {
	
	public $name;
	public $Creator;
	
	// field configuration
	protected $length = FLO_FIELD_LENGTH;
	protected $required = OPTIONAL;
	protected $default_value = NULL;
	public $user_value;
	
	// appearance
	public $label; // displayed field name (=html:label)
	public $title; // tooltip (=html:title)
	public $language; // field language (=html:lang)
	public $direction; // writing direction (=html:dir)
	public $description; // a short help text
	public $htmltype; // text|radio|select|... (=html:type)
	public $css_class; // =html:class
	public $css_style; // =html:style
	public $appended_to; // field name of the first field in row
	public $autowidth = false; // set field width automatically
	
	// sub-object arrays
	public $Conditions = array();
	
	// validation properties
	public $error = NULL;
	public $valid = NULL;
	
	// PROPERTIES -------------------------------------------------------------
	
	public function is_hidden () {
		return ($this->htmltype == 'hidden');
	}
	
	public function is_not_hidden () {
		return ($this->htmltype != 'hidden');
	}
	
	protected function makeRequired () {
		$this->required = REQUIRED;
	}
	
	protected function makeOptional () {
		$this->required = OPTIONAL;
	}
	
	public function getId () {
		return ($this->Creator->name.'-'.$this->name);
	}
	
	// APPEARANCE -------------------------------------------------------------
	
	public function setLabel ($label) {
		$this->label = $label;
		$this->Creator->debuglog->Write(DEBUG_INFO,". . LABEL \"$label\" set");
		return $this;
	}
	
	public function setTitle ($title) {
		$this->title = $title;
		return $this;
	}
	
	public function setLanguage ($language) {
		$this->language = $language;
		return $this;
	}
	
	public function setDirection ($direction) {
		$this->direction = $direction;
		return $this;
	}
	
	public function setDescription ($description) {
		$this->description = $description;
		return $this;
	}
	
	public function setClass ($css_class) {
		$this->css_class = $css_class;
		return $this;
	}
	
	public function setStyle ($css_style) {
		$this->css_style = $css_style;
		return $this;
	}
	
	// APPEARANCE : FIELD GROUPS ----------------------------------------------
	
	public function appendTo ($field_name) {
		$this->appended_to = $field_name;
		return $this;
	}
	
	public function unappend () {
		$this->appended_to = NULL;
		return $this;
	}
	
	public function is_appended () {
		return ($this->appended_to);
	}
	
	// CONDITIONS -------------------------------------------------------------
	
	public function addCondition () {
		require_once 'field-condition.php';
		$args = func_get_args();
		$type = array_shift($args);
		require_once 'field-condition_'.strtolower($type).'.php';
		array_unshift($args,$this);
		$this->Conditions[] = call_user_func_array('FieldCondition_'.$type.'::create',$args);
		return $this;
	}
	
	protected function CheckConditions () {
		reset($this->Conditions);
		while (list($index, $Condition) = each($this->Conditions)) {
			$this->error = $Condition->Check($this->user_value);
			if ($this->error) {
				break;
			}
		}
		return $this->error;
	}
	
	// EVALUATION -------------------------------------------------------------
	
	public function evaluatePost () {
		switch (get_class($this)) {
			case 'Field_Checkbox':
				// checkboxes require special treatment
				$this->user_value = isset($_POST[$this->name])
					? $this->default_value
					: NULL;
				$this->Creator->debuglog->Write(DEBUG_INFO,'. field "'.$this->name.'" is '.(is_null($this->user_value)?'not checked':'checked'));
				break;
			case 'Field_MultipleCheckbox':
				// multiple checkboxes require special treatment
				$this->user_value = isset($_POST[$this->name])
					? $_POST[$this->name]
					: NULL;
				$this->Creator->debuglog->Write(DEBUG_INFO,'. field "'.$this->name.'" contains '.(is_null($this->user_value)?'nothing':implode(',',$this->user_value)));
				break;
			case 'Field_MultipleSelect':
				// multiple selects require special treatment
				$this->user_value = isset($_POST[$this->name])
					? $_POST[$this->name]
					: NULL;
				$this->Creator->debuglog->Write(DEBUG_INFO,'. field "'.$this->name.'" contains '.(is_null($this->user_value)?'nothing':implode(',',$this->user_value)));
				break;
			case 'Field_StaticText':
				// static fields do not need to be evaluated
				$this->Creator->debuglog->Write(DEBUG_INFO,'. field "'.$this->name.'" contains HTML');
				break;
			case 'Field_Subtable':
				$this->user_value = array();
				if (isset($_POST[$this->name.'-subtable'])) {
					$this->user_value = $_POST[$this->name.'-subtable'];
				}
				if (isset($_POST[$this->name]) && $_POST[$this->name]!='') {
					$this->user_value[] = stripslashes($_POST[$this->name]);
				}
				if (count($this->user_value) == 0) {
					$this->user_value = NULL;
				}
				$this->Creator->debuglog->Write(DEBUG_INFO,'. field "'.$this->name.'" contains '.(is_null($this->user_value)?'nothing':implode(',',$this->user_value)));
				break;
			default:
				(isset($_POST[$this->name]) && $_POST[$this->name]!='')
					? $this->user_value = stripslashes($_POST[$this->name])
					: $this->user_value = NULL;
				$this->Creator->debuglog->Write(DEBUG_INFO,'. field "'.$this->name.'" contains '.(is_null($this->user_value)?'nothing':'"'.$this->user_value.'"'));
				break;
		}
		if ($this->required && is_null($this->user_value)) {
			// FIELD IS REQUIRED BUT EMPTY
			$this->Creator->debuglog->Write(DEBUG_INFO,". . INVALID (value is required)");
			$this->error = FLO_REQUIRED_FIELD;
		} else {
			// USER INPUT RECEIVED
			$this->error = $this->CheckConditions();
			if (!$this->error) {
				$this->Creator->debuglog->Write(DEBUG_INFO,". . valid");
				$this->valid = true;
			}
			else {
				$this->Creator->debuglog->Write(DEBUG_INFO,". . INVALID (conditions not met)");
			}
		}
		return (is_null($this->error));
	}
	
	public function evaluateGet () {
		switch (get_class($this)) {
			case 'Field_Checkbox':
				// checkboxes require special treatment
				$this->user_value = isset($_GET[$this->name])
					? $this->default_value
					: NULL;
				$this->Creator->debuglog->Write(DEBUG_INFO,'. field "'.$this->name.'" is '.(is_null($this->user_value)?'not checked':'checked'));
				break;
			case 'Field_MultiCheckbox':
				// multiple checkboxes require special treatment
				$this->user_value = isset($_GET[$this->name])
					? $_GET[$this->name]
					: NULL;
				$this->Creator->debuglog->Write(DEBUG_INFO,'. field "'.$this->name.'" contains '.(is_null($this->user_value)?'nothing':implode(',',$this->user_value)));
				break;
			case 'Field_MultiSelect':
				// multiple selects require special treatment
				$this->user_value = isset($_GET[$this->name])
					? $_GET[$this->name]
					: NULL;
				$this->Creator->debuglog->Write(DEBUG_INFO,'. field "'.$this->name.'" contains '.(is_null($this->user_value)?'nothing':implode(',',$this->user_value)));
				break;
			case 'Field_StaticText':
				// static fields do not need to be evaluated
				$this->Creator->debuglog->Write(DEBUG_INFO,'. field "'.$this->name.'" contains HTML');
				break;
			case 'Field_Subtable':
				$this->user_value = isset($_GET[$this->name])
					? $_GET[$this->name]
					: NULL;
				$this->Creator->debuglog->Write(DEBUG_INFO,'. field "'.$this->name.'" contains '.(is_null($this->user_value)?'nothing':implode(',',$this->user_value)));
				break;
			default:
				(isset($_GET[$this->name]) && $_GET[$this->name]!='')
					? $this->user_value = stripslashes($_GET[$this->name])
					: $this->user_value = NULL;
				$this->Creator->debuglog->Write(DEBUG_INFO,'. field "'.$this->name.'" contains '.(is_null($this->user_value)?'nothing':'"'.$this->user_value.'"'));
				break;
		}
		if ($this->required && is_null($this->user_value)) {
			// FIELD IS REQUIRED BUT EMPTY
			$this->Creator->debuglog->Write(DEBUG_INFO,". . INVALID (value is required)");
			$this->error = FLO_REQUIRED_FIELD;
		} else {
			// USER INPUT RECEIVED
			$this->error = $this->CheckConditions();
			if (!$this->error) {
				$this->Creator->debuglog->Write(DEBUG_INFO,". . valid");
				$this->valid = true;
			}
			else {
				$this->Creator->debuglog->Write(DEBUG_INFO,". . INVALID (conditions not met)");
			}
		}
		return (is_null($this->error));
	}
	
	public function evaluateGet2 () {
		// try to fill in GET value
		$this->error = NULL;
		if (isset($_GET[$this->name])) {
			// LOOK FOR GET PARAMETER
			$this->user_value = $_GET[$this->name];
			$this->error = $this->CheckConditions();
			if (!$this->error) {
				$this->Creator->debuglog->Write(DEBUG_INFO,". VALUE for '$this->name' accepted");
				$this->valid = true;
			}
			else {
				$this->Creator->debuglog->Write(DEBUG_INFO,". VALUE for '$this->name' not accepted");
				$this->user_value = $this->default_value;
			}
		}
		return (is_null($this->error));
	}
	
	// HTML OUTPUT ------------------------------------------------------------
	
	public function HTMLLeadIn () {
		// contains:
		// - field label
		// - "required" symbol
		$leadin = NULL;
		if (!$this->is_hidden()) {
			if (get_class($this) != 'Field_StaticText') {
				$leadin .= "\t<tr class=\"field".($this->error?' error':'').($this->valid?' valid':'').($this->required?' required':'')."\">".PHP_EOL;
				$leadin .= "\t\t<td>";
				$leadin .= "<label for=\"{$this->getId()}\">";
				$leadin .= $this->label?$this->label:$this->name;
				$leadin .= "</label>";
				$leadin .= "</td>".PHP_EOL;
				$leadin .= "\t\t<td>";
				if ($this->required) $leadin .= '<div class="required">'.FLO_REQUIRED_FIELD_SYMBOL.'</div>';
				$leadin .= "</td>".PHP_EOL;
				$leadin .= "\t\t<td>".PHP_EOL;
			}
			else {
				$leadin .= "\t\t<td colspan=\"4\">".PHP_EOL;
			}
			// cell content starts after leadin
		}
		return $leadin;
	}
	
	public function HTMLLeadOut () {
		// contains:
		// - cell delimiters
		// - field description
		// - validation messages
		$leadout = NULL;
		if (!$this->is_hidden()) {
			if (get_class($this) != 'Field_StaticText') {
				if ($this->description) $leadout .= "\t\t\t<div class=\"description\">$this->description</div>".PHP_EOL;
				$leadout .= "\t\t</td>".PHP_EOL;
				if ($this->error) $leadout .= "\t\t<td><div class=\"error\">$this->error</div></td>".PHP_EOL;
				elseif ($this->valid) $leadout .= "\t\t<td><div class=\"valid\">".FLO_FIELD_IS_VALID."</div></td>".PHP_EOL;
			}
			$leadout .= "\t</tr>".PHP_EOL;
		}
		return $leadout;
	}
	
	protected function HTMLTitle () {
		$title = NULL;
		if ($this->title) $title .= " title=\"$this->title\"";
		return $title;
	}
	
	protected function HTMLClass () {
		$class = NULL;
		if ($this->css_class) $class = " class=\"$this->css_class\"";
		return $class;
	}
	
	protected function HTMLStyle () {
		$style = NULL;
		// AUTO WIDTH goes first, so it can be overridden by user style
		if ($this->autowidth && FLO_AUTO_FIELD_WIDTH) {
			$auto_width = $this->length*FLO_AUTO_FIELD_WIDTH_FACTOR+FLO_AUTO_FIELD_WIDTH_MIN;
			if ($auto_width>FLO_AUTO_FIELD_WIDTH_MAX) $auto_width=FLO_AUTO_FIELD_WIDTH_MAX;
			$style .= "width:{$auto_width}px";
		}
		/*
		if (get_class($this) != 'Field_Upload') {
			$style .= 'display:none;';
		}
		*/
		if ($this->css_style) {
			if (isset($style)) $style .= '; ';
			$style .= $this->css_style;
		}
		return (isset($style) ? " style=\"$style\"" : NULL);
	}
	
	// METHOD CALL PASS -------------------------------------------------------

	public function addField () {
		$args = func_get_args();
		return call_user_func_array(array($this->Creator,'addField'),$args);
	}	
	
	public function addButton () {
		$args = func_get_args();
		return call_user_func_array(array($this->Creator,'addButton'),$args);
	}	
	
	public function addImageButton () {
		$args = func_get_args();
		return call_user_func_array(array($this->Creator,'addImageButton'),$args);
	}	
	
} // end class Field

?>
