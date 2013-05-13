<?php

class FieldCondition_Replace extends FieldCondition {

	static public function create() {
		return new FieldCondition_Replace (func_get_args());
	}
	
	public function Check ($value) {
		if ($new_value = preg_replace($this->parameters[0],$this->parameters[1],$value)) {
			$this->Creator->user_value = $new_value;
		}
		else {
			$this->error = FLO_INVALID_FORMAT;
		}
		return ($this->error);
	}

}

?>
