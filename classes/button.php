<?php
// last known update: 2013-01-25

class Button {
	private $Creator;
	protected $type;
	protected $label;
	protected $target;
	
	protected $title;
	protected $htmltype;
	
	// CONSTRUCTORS --------------------------------------------------------------
	
	protected function __construct ( $Creator, $type, $label = DEFAULT_LABEL, $target = NULL) {
		$this->Creator = $Creator;
		$this->type = $type;
		$this->setLabel($label);
		$this->target = $target;
		switch ($this->type) {
			case APPLY:		$this->htmltype = 'submit'; break;
			case RESET:		$this->htmltype = 'reset';	break;
			case SUBMIT:	$this->htmltype = 'submit'; break;
			default:		$this->htmltype = 'button'; break;
		}
	}
	
	static public function create () {
		// create ( Creator , type [, label [, target ]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Button ($args[0],$args[1]);
			case 3: return new Button ($args[0],$args[1],$args[2]);
			case 4: return new Button ($args[0],$args[1],$args[2],$args[3]);
			default: DebugWarning ('addButton ignored: invalid number of arguments');
		}
	}
	
	// PROPERTIES ----------------------------------------------------------------
	
	protected function setLabel ($label) {
		if (is_null($label)) {
			switch ($this->type) {
				case APPLY:		$this->label = FLO_LABEL_APPLY;		break;
				case BACK:		$this->label = FLO_LABEL_BACK;		break;
				case CANCEL:	$this->label = FLO_LABEL_CANCEL;	break;
				case REFRESH:	$this->label = FLO_LABEL_REFRESH;	break;
				case RELOAD:	$this->label = FLO_LABEL_RELOAD;	break;
				case RESET:		$this->label = FLO_LABEL_RESET;		break;
				case SUBMIT:	$this->label = FLO_LABEL_SUBMIT;	break;
				default:		$this->label = $this->target;		break;
			}
		}
		else {
			$this->label = $label;
		}
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function setTarget ($target) {
		$this->target = $target;
	}
	
	// HTML OUTPUT METHODS -------------------------------------------------------
	
	protected function HTMLLeadIn () {
		$lead_in = NULL;
		$lead_in .= "\t\t\t<button name=\"{$this->type}-button\" type=\"".$this->htmltype.'" class="button '.$this->type.'-button"';
		switch ($this->type) {
			case BACK:		$lead_in .= ' onclick="location.href=\''.$_SESSION['flotilla']['last_page'].'\'"'; break;
			case CANCEL:	$lead_in .= ' onclick="location.href=\''.(isset($this->target)?$this->target:'.').'\'"'; break;
			case REFRESH:	$lead_in .= ' onclick="location.href=\''.$_SERVER['SCRIPT_NAME'].'\'"'; break;
			case RELOAD:	$lead_in .= ' onclick="location.reload()"'; break;
			case LINK:
				$this->target = preg_replace('/(\{(\w*)\})/e',"\$this->Creator->Fields['$2']->user_value",$this->target);
				$lead_in .= ' onclick="location.href=\''.$this->target.'\'"';
				break;
		}
		$lead_in .= ' title="';
		switch ($this->type) {
			case APPLY:		$lead_in .= FLO_TITLE_APPLY;	break;
			case BACK:		$lead_in .= FLO_TITLE_BACK;		break;
			case CANCEL:	$lead_in .= FLO_TITLE_CANCEL;	break;
			case REFRESH:	$lead_in .= FLO_TITLE_REFRESH;	break;
			case RELOAD:	$lead_in .= FLO_TITLE_RELOAD;	break;
			case RESET:		$lead_in .= FLO_TITLE_RESET;	break;
			case SUBMIT:	$lead_in .= FLO_TITLE_SUBMIT;	break;
			default:		$lead_in .= $this->target;		break;
		};
		$lead_in .= '"';
		$lead_in .= '>';
		return $lead_in;
	}
	
	protected function HTMLLeadOut () {
		$lead_out = NULL;
		$lead_out .= '</button>';
		return $lead_out;
	}
	
	protected function HTMLContent () {
		return $this->label;
	}
	
	public function HTMLOutput () {
		return $this->HTMLLeadIn().$this->HTMLContent().$this->HTMLLeadOut().PHP_EOL;
	}

}

?>
