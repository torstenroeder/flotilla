<?php

class FieldCondition_allowedChars extends FieldCondition {

	static public function create() {
		return new FieldCondition_allowedChars (func_get_args());
	}
	
	public function Check ($value) {
		switch ($this->parameters[0]) {
			case ALPHABETIC:
				if (!preg_match('/^[a-z ]*$/i',$value))
					$this->error = FLO_ALPHABETIC_CHARS_ONLY;
				break;
			case ALPHANUMERIC:
				if (!preg_match('/^[a-z 0-9\-]*$/i',$value))
					$this->error = FLO_ALPHANUMERIC_CHARS_ONLY;
				break;
			case CIPHERS:
				if (!preg_match('/^[0-9]*$/',$value))
					$this->error = FLO_CIPHERS_ONLY;
				break;
			case NUMERIC:
				if (!preg_match('/^[0-9\-+,.]*$/',$value))
					$this->error = FLO_NUMERIC_CHARS_ONLY;
				break;
			case PHONE:
				if (!preg_match('/^[0-9\-+.()\/]*$/',$value))
					$this->error = FLO_PHONE_CHARS_ONLY;
				break;
			case DOMAIN:
				if (!preg_match('/^[a-z0-9\-.]*$/i',$value))
					$this->error = FLO_DOMAIN_CHARS_ONLY;
				break;
			case EMAIL:
				if (!preg_match('/^[a-z0-9\-+._@]*$/i',$value))
					$this->error = FLO_EMAIL_CHARS_ONLY;
				break;
			case URL:
				if (!preg_match('/^[a-z0-9\-./_]*$/i',$value))
					$this->error = FLO_URL_CHARS_ONLY;
				break;
			case REGEX:
				if (!preg_match('/^['.$this->parameters[0].']*$/i',$value))
					$this->error = FLO_ALLOWED_CHARS_ONLY;
				break;
			case NULL:
				$this->Creator->Creator->debuglog->Write(DEBUG_ERROR,'. condition name not defined');
				break;
			default:
				$this->Creator->Creator->debuglog->Write(DEBUG_ERROR,'. condition name unknown');
				break;
		}
		return ($this->error);
	}

} // end class

?>
