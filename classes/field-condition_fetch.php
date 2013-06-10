<?php

class FieldCondition_Fetch extends FieldCondition {

	static public function create() {
		return new FieldCondition_Fetch (func_get_args());
	}
	
	public function Check ($value) {
		$this->Creator->Creator->debuglog->Write(DEBUG_INFO,'. . trying to fetch content from "'.$this->parameters[2].'"');
		if ($new_value = preg_replace($this->parameters[0],$this->parameters[1],$this->Creator->Creator->Fields[$this->parameters[2]]->user_value)) {
			$this->Creator->user_value = $new_value;
		}
		else {
			$this->error = FLO_INVALID_FORMAT;
		}
		return ($this->error);
	}

}

?>
