<?php

class FieldCondition_Type extends FieldCondition {
	
	static public function create() {
		return new FieldCondition_Type (func_get_args());
	}
	
	public function Check ($value) {
		if (!is_numeric($value))
			$this->error = FLO_NOT_A_NUMBER;
		if (!$this->error) {
			switch ($this->parameters[0]) {
				case INTEGER:
					if (intval($value)!=$value) 
						$this->error = FLO_TYPE_NOT_INTEGER;
					break;
				case REAL:
					if (floatval($value)!=$value)
						$this->error = FLO_TYPE_NOT_REAL;
					break;
			}
		}
		return ($this->error);
	}

} // end class FieldCondition_Type

?>
