<?php

class FieldCondition_Date extends Field {

	static public function create() {
		return new FieldCondition_Date (func_get_args());
	}
	
	public function Check ($value) {
		switch ($this->parameters[0]) {
			case ENGLISH:
				if (!preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/',$value))
					$this->error = FLO_INVALID_ENGLISH_DATE;
				break;
			case GERMAN:
				if (!preg_match('/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/',$value))
					$this->error = FLO_INVALID_GERMAN_DATE;
				break;
			default:
				// USER FORMAT
				if (!preg_match($this->parameter,$value))
					$this->error = FLO_INVALID_DATE;
				break;
		}
		return ($this->error);
	}
}

?>
