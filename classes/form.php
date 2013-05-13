<?php
// last update: 2013-01-29

class Form {
	
	public $name;
	
	// sub-objects
	public $Fields = array();
	public $Conditions = array();
	public $Buttons = array();
	public $Actions = array();
	public $Connection;
	
	// database interoperation
	protected $primary_key;
	
	// appearance
	protected $label;
	protected $css_class;
	protected $css_style;
	
	// general functionality
	protected $action = '';
	protected $method = POST; // post|get
	protected $mode = 'edit'; // edit|view|review
	
	// evaluation
	protected $error = NULL;
	protected $valid;
	
	// debugging
	public $debuglog;
	
	// CONSTRUCTORS -----------------------------------------------------------
	
	public function __construct () {
		// Form ( name [, action [, method ]] )
		$args = func_get_args();
		$this->debuglog = new DebugLog(DEBUG_LEVEL);
		if (isset($args[0])) $this->name = $args[0];
		else Error ('missing Form->name');
		if (isset($args[1])) $this->action = $args[1];
		if (isset($args[2])) $this->method = $args[2];
		$this->debuglog->Write(DEBUG_INFO,'new Form created');
		// are last page and referer already defined?
		if (isset ($_SESSION['flotilla']['last_page'])) {
			if (isset ($_SERVER['HTTP_REFERER'])) {
				// is last page different from the referer?
				if ($_SESSION['flotilla']['last_page'] != $_SERVER['HTTP_REFERER']) {
					// is referer different from the current url? 
					if (
						((!isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'off')) && $_SERVER['HTTP_REFERER'] != 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'])
						||
						((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') && $_SERVER['HTTP_REFERER'] != 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'])
					) {
						$_SESSION['flotilla']['last_page'] = $_SERVER['HTTP_REFERER'];
					}
				}
			}
			// in all other cases: last page remains the same
		}
		else {
			$_SESSION['flotilla']['last_page'] = '.';
		}
		return $this;
	}
	
	// CONNECTION -------------------------------------------------------------
	
	public function addConnection () {
		require_once 'connection.php';
		// addConnection ( type , host , user , pass , db_name )
		$args = func_get_args();
		$type = array_shift($args);
		require_once 'connection_'.strtolower($type).'.php';
		array_unshift($args,$this);
		return $this->Connection = call_user_func_array('Connection_'.$type.'::create',$args);
	}
	
	// FORM APPEARANCE --------------------------------------------------------
	
	public function setLabel ($label) {
		$this->label = $label;
		return $this;
	}
	
	public function getLabel () {
		if ($this->label!='') {
			return "\t".'<tr><th colspan="4" class="form-header">'.($this->label?$this->label:$this->name).'</th></tr>'.PHP_EOL;
		}
	}
	
	public function setTitle ($title) {
		$this->title = $title;
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
	
	public function setAppendMode ($append_mode) {
		$this->append_mode = $append_mode;
		return $this;
	}
	
	// FIELDS -----------------------------------------------------------------
	
	public function addField () {
		require_once 'field.php';
		// addField ( name , type [, parameter_1 [, parameter_2 ... ]] )
		$args = func_get_args();
		$type = $args[1];
		$args[1] = $args[0];
		$args[0] = $this;
		require_once 'field_'.strtolower($type).'.php';
		return $this->Fields[$args[1]] = call_user_func_array('Field_'.$type.'::create',$args);
	}
	
	public function getFieldValue ($field_name) {
		return $this->Fields[$field_name]->user_value;
	}
	
	public function is_required ($field_name) {
		return $this->Fields[$field_name]->is_required();
	}
	
	public function is_optional ($field_name) {
		return $this->Fields[$field_name]->is_optional();
	}
	
	// FIELD SUB CLASSES ------------------------------------------------------
	
	public function addSelectOption () {
		$args = func_get_args();
		$this->Fields[$args[0]]->Options[] = call_user_func_array('Option::create',$args);
		return $this;
	}
	
	public function addRadioButton () {
		$args = func_get_args();
		$this->Fields[$args[0]]->RadioButtons[] = call_user_func_array('RadioButton::create',$args);
		return $this;
	}
	
	public function addSubtableRelation () {
		$args = func_get_args();
		$this->Fields[$args[0]]->Relations[] = call_user_func_array('SubtableRelation::create',$args);
		return $this;
	}
	
	// BUTTONS ----------------------------------------------------------------
	
	public function addButton () {
		require_once 'button.php';
		$args = func_get_args();
		array_unshift($args,$this);
		$this->Buttons[] = call_user_func_array('Button::create',$args);
		return $this;
	}
	
	public function addImageButton () {
		require_once 'button.php';
		require_once 'button_image.php';
		$args = func_get_args();
		array_unshift($args,$this);
		$this->Buttons[] = call_user_func_array('ImageButton::create',$args);
		return $this;
	}
	
	// FORM CONDITIONS --------------------------------------------------------
	
	public function addCondition () {
		require_once 'form-condition.php';
		$args = func_get_args();
		array_unshift($args,$this);
		$this->Conditions[] = call_user_func_array('FormCondition::create',$args);
		return $this;
	}
	
	protected function evaluateConditions () {
		$this->error = NULL;
		reset($this->Conditions);
		while (list($index, $condition) = each($this->Conditions)) {
			if (!$condition->evaluate()) {
				$this->error = $condition->error_message;
			}
		}
		return (is_null($this->error));
	}
	
	// ACTIONS ----------------------------------------------------------------
	
	public function addAction () {
		require_once 'action.php';
		$args = func_get_args();
		$type = strtolower(array_shift($args));
		require_once 'action_'.$type.'.php';
		array_unshift($args,$this);
		$this->Actions[] = call_user_func_array('Action_'.ucfirst($type).'::create',$args);
		return $this;
	}
	
	protected function onLoad () {
		reset($this->Actions);
		while (list($index, $action) = each($this->Actions)) {
			if (method_exists($action,'onLoad')) $action->onLoad();
		}
	}
	
	protected function onSubmit () {
		reset($this->Actions);
		while (list($index, $action) = each($this->Actions)) {
			if (method_exists($action,'onSubmit')) $action->onSubmit();
		}
	}
	
	// EVALUATION -------------------------------------------------------------
	
	protected function evaluatePost () {
		if (!empty($_POST)) {
			reset($this->Fields);
			while (list($index, $field) = each($this->Fields)) {
				if (!$field->evaluatePost()) {
					$this->error = FLO_INVALID_POST;
				}
			}
		}
		return (is_null($this->error));
	}
	
	protected function evaluateGet () {
		if (!empty($_GET)) {
			reset($this->Fields);
			while (list($index, $field) = each($this->Fields)) {
				if (!$field->evaluateGet()) {
					$this->error = FLO_CHECK_PARAMETER;
				} else {
					if ($this->primary_key == $field->name) {
						$this->SetPrimaryKeyValue ($_GET[$field->name]);
					}
				}
			}
		}
		return (is_null($this->error));
	}
	
	protected function evaluate () {
		if (!empty($_POST)) {
			// something was posted
			$this->debuglog->Write(DEBUG_INFO,'POST data found');
			if ($this->evaluatePost()) {
				$this->debuglog->Write(DEBUG_INFO,'. POST data valid');
				if ($this->evaluateConditions()) {
					$this->onSubmit();
				}
			} else {
				$this->debuglog->Write(DEBUG_INFO,'. POST data not valid');
			}
		}
		else {
			// nothing was posted
			if (!empty($_GET)) {
				$this->debuglog->Write(DEBUG_INFO,'GET data found');
				if (!$this->evaluateGet()) {
					$this->debuglog->Write(DEBUG_INFO,'. GET data not valid');
				}
				$this->onLoad();
			}
			else {
				$this->onLoad();
			}
		}
		return (is_null($this->error));
	}
	
	// HTML OUTPUT ------------------------------------------------------------
	
	protected function HTMLOutput () {
		$output = '<!-- the following code was generated by Flotilla -->'.PHP_EOL;
		// javascript
		if (class_exists('Field_Subtable')) {
			$output .= '<script src="flotilla/javascript/subtable.js" type="text/javascript"></script>'.PHP_EOL;
		}
		// form header
		$output .= '<form';
		$output .= ' name="'.$this->name.'"';
		$output .= ' id="'.$this->name.'"';
		$output .= ' method="'.$this->method.'"';
		$output .= ' action="'.$this->action.'"';
		if (class_exists('Field_Upload')) {
			$output .= ' enctype="multipart/form-data"';
		}
		$output .= ' class="form'.($this->css_class?' '.$this->css_class:'').'"';
		if ($this->css_style) $output .= ' style="'.$this->css_style.'"';
		$output .= '>'.PHP_EOL;
		// form table
		$output .= '<table>'.PHP_EOL;
		// form table header
		$output .= $this->getLabel();
		// form table body: fields
		reset($this->Fields);
		while (list($field_name, $field) = each($this->Fields)) {
			if (!$field->is_appended()) {
				$output .= $field->HTMLLeadIn();
				$output .= $field->HTMLOutput();
				reset($this->Fields);
				while (list($appended_field_name, $appended_field) = each($this->Fields)) {
					if ($appended_field->appended_to == $field_name) {
						$output .= "\t\t<label for=\"$appended_field->name\">";
						if ($appended_field->label) $output .= $appended_field->label;
						$output .= '</label>'.PHP_EOL;
						$output .= $appended_field->HTMLOutput();
					}
				}
				reset($this->Fields);
				while (key($this->Fields) != $field_name) { 
					next($this->Fields);
				}
				next($this->Fields);
				$output .= $field->HTMLLeadOut();
			}
		}
		// form table message: error
		if ($this->error) $output .= "\t<tr><td colspan=\"2\"></td><td colspan=\"2\"><div class=\"error\">$this->error</div></td></tr>".PHP_EOL;
		elseif ($this->valid) $output .= "\t<tr><td colspan=\"2\"></td><td colspan=\"2\"><div class=\"valid\">".FLO_FORM_IS_VALID."</div></td></tr>".PHP_EOL;
		// form table foot: buttons
		$output .= "\t<tr>".PHP_EOL."\t\t<td colspan=\"2\"></td>".PHP_EOL."\t\t<td colspan=\"2\">".PHP_EOL;
		reset($this->Buttons);
		while (list($index, $Button) = each($this->Buttons)) {
			if ($Button->getType() == RELOAD) {
				if (get_class($this) == 'DatabaseForm' && $this->GetPrimaryKeyValue()) {
					$Button->setTarget('?'.$this->GetPrimaryKey().'='.$this->GetPrimaryKeyValue());
				}
				else {
					$Button->setTarget(' ');
				}
			}
			$output .= $Button->HTMLOutput();
		}
		$output .= "\t\t</td>".PHP_EOL."\t</tr>".PHP_EOL;
		// end form table
		$output .= '</table>'.PHP_EOL;
		// end form
		$output .= '</form>'.PHP_EOL;
		//$output .= $this->debuglog->Show();
		$output .= '<!-- thank you for using Flotilla -->'.PHP_EOL;
		return $output;
	}
	
	// RUN --------------------------------------------------------------------
	
	public function run () {
		$this->evaluate();
		//print_r ($_POST);
		//print_r ($_FILES);
		//print_r ($_SESSION);
		//print_r ($_COOKIE);
		return $this->HTMLOutput();
	}
	
} // end class FORM

?>
