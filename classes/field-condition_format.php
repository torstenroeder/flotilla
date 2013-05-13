<?php

class FieldCondition_Format extends FieldCondition {

	static public function create() {
		return new FieldCondition_Format (func_get_args());
	}
	
	public function Check ($value) {
		switch ($this->parameters[0]) {
			case DOMAIN:
				if (!preg_match('/^[a-z0-9.-]+\.[a-z]{2,6}$/i',$value))
					$this->error = FLO_INVALID_DOMAIN_FORMAT;
				break;
			case EMAIL:
				if (!preg_match('/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,6}$/i',$value))
					$this->error = FLO_INVALID_EMAIL_FORMAT;
				break;
			case WEBSITE:
				if (!preg_match('/^(http|https|ftp)\://([a-z0-9.-]+\.[a-z]{2,6})(\/[a-z0-9.-]+)*/i',$value))
					$this->error = FLO_INVALID_WEBSITE_FORMAT;
				break;
			default:
				if (!preg_match($this->parameters[0],$value))
					$this->error = FLO_INVALID_FORMAT;
				break;
		}
		return ($this->error);
	}

}

?>
