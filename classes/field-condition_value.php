<?php

class FieldCondition_Value extends FieldCondition {

	static public function create() {
		return new FieldCondition_Value (func_get_args());
	}
	
	public function Check ($value) {
		if (!is_numeric($value))
			$this->error = FLO_NOT_A_NUMBER;
		if (!$this->error) {
			switch ($this->parameters[0]) {
				case POSITIVE:
					if (intval($value)!=$value || intval($value)<=0)
						$this->error = FLO_VALUE_NOT_POSITIVE;
					break;
				case NEGATIVE:
					if (intval($value)!=$value || intval($value)>=0)
						$this->error = FLO_VALUE_NOT_NEGATIVE;
					break;
				case NOTPOSITIVE:
					if (intval($value)!=$value || intval($value)>0)
						$this->error = FLO_VALUE_NOT_NOTPOSITIVE;
					break;
				case NOTNEGATIVE:
					if (intval($value)!=$value || intval($value)<0)
						$this->error = FLO_VALUE_NOT_NOTNEGATIVE;
					break;
				case NOTZERO:
					if ($value == 0)
						$this->error = FLO_VALUE_IS_ZERO;
					break;
				case MIN:
					if ($value < $this->parameters[1])
						$this->error = FLO_VALUE_TOO_LOW;
					break;
				case MAX:
					if ($value > $this->parameters[1])
						$this->error = FLO_VALUE_TOO_HIGH;
					break;
				case GREATER_THAN:
					if ($value <= $this->parameters[1])
						$this->error = FLO_VALUE_TOO_LOW;
					break;
				case LESS_THAN:
					if ($value >= $this->parameters[1])
						$this->error = FLO_VALUE_TOO_HIGH;
					break;
				case EQUALS:
					if ($value != $this->parameters[1])
						$this->error = FLO_VALUE_NOT_EQUAL;
					break;
			}
		}
		return ($this->error);
	}

}

?>
