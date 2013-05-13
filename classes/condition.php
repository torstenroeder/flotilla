<?php

class Condition {
	// user defined properties
	public $type;
	public $parameter;
	// internal properties
	protected $error;
	
	public function __construct ($type, $parameter) {
		if (isset($type)) $this->type = $type;
		else Error ('Missing CONDITION type');
		if (isset($parameter)) $this->parameter = $parameter;
		else Error ('Missing CONDITION parameter');
	}
	
	public function Check ($value) {
		if ($value!='') {
			DebugInfo (" . . TESTING for $this->type: $this->parameter");
			switch ($this->type) {
				// condition:  EXCLUDED_CHARS
				// parameters: no predefined parameters
				// user input: yes (a string containing the excluded characters)
				case EXCLUDED_CHARS:
					switch ($this->parameter) {
						default:
							if (strpbrk($value,$this->parameter))
								$this->error = 'Die Eingabe enthält ungültige Zeichen.';
							break;
						}
					break;
				// end case EXCLUDED_CHARS
				
				// condition:  ALLOWED_CHARS
				// parameters: ALPHABETIC, ALPHANUMERIC, NUMERIC, CIPHERS, PHONE, DOMAIN, EMAIL, URL
				// user input: yes (a string containing the allowed characters)
				case ALLOWED_CHARS:
					switch ($this->parameter) {
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
							if (!preg_match('/^[0-9\-+,.()\/]*$/',$value))
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
						default:
							if (!preg_match('/^['.$this->parameter.']*$/i',$value))
								$this->error = FLO_ALLOWED_CHARS_ONLY;
							break;
					}
					break;
				// end case ALLOWED_CHARS
				
				// condition:  FORMAT
				// parameters: DOMAIN, EMAIL
				case FORMAT:
					switch ($this->parameter) {
						case DOMAIN:
							if (!preg_match('/^[a-z0-9.-]+\.[a-z]{2,6}$/i',$value))
								$this->error = FLO_INVALID_DOMAIN_FORMAT;
							break;
						case EMAIL:
							if (!preg_match('/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,6}$/i',$value))
								$this->error = FLO_INVALID_EMAIL_FORMAT;
							break;
					}
					break;
				// end case FORMAT
				
				// condition:  REGEX
				// parameters: a regular expression
				// user input: yes, required
				case REGEX:
					if (!preg_match($this->parameter,$value))
						$this->error = FLO_INVALID_REGEX;
					break;
				// end case REGEX
				
				// condition:  DATE
				// parameters: ENGLISH, GERMAN
				// user input: yes (a regular expression)
				case DATE:
					switch ($this->parameter) {
						case ENGLISH:
							if (!preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}$/',$value))
								$this->error = FLO_INVALID_ENGLISH_DATE;
							break;
						case GERMAN:
							if (!preg_match('/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{2,4}$/',$value))
								$this->error = FLO_INVALID_GERMAN_DATE;
							break;
						default:
							// USER FORMAT
							if (!preg_match($this->parameter,$value))
								$this->error = FLO_INVALID_DATE;
							break;
					}
					break;
				// end case DATE
				
				// condition:  NUMERIC
				// parameters: NATURAL, POSITIVE, NONPOSITIVE, NEGATIVE, NONNEGATIVE
				// user input: no
				case NUMERIC:
					if (!is_numeric($value))
						$this->error = FLO_NO_NUMBER;
					if (!$this->error) {
						switch ($this->parameter) {
							case INTEGER:
								if (intval($value)!=$value) 
									$this->error = FLO_NO_INTEGER;
								break;
							case POSITIVE_INT:
								if (intval($value)!=$value || intval($value)<=0)
									$this->error = FLO_NO_POSITIVE_INT;
								break;
							case NEGATIVE_INT:
								if (intval($value)!=$value || intval($value)>=0)
									$this->error = FLO_NO_NEGATIVE_INT;
								break;
							case NONPOSITIVE_INT:
								if (intval($value)!=$value || intval($value)>0)
									$this->error = FLO_NO_NONPOSITIVE_INT;
								break;
							case NONNEGATIVE_INT:
								if (intval($value)!=$value || intval($value)<0)
									$this->error = FLO_NO_NONNEGATIVE_INT;
								break;
							case POSITIVE_REAL:
								if ($value<=0)
									$this->error = FLO_NO_POSITIVE_REAL;
								break;
							case NEGATIVE_REAL:
								if ($value>=0)
									$this->error = FLO_NO_NEGATIVE_REAL;
								break;
							case NONPOSITIVE_REAL:
								if ($value>0)
									$this->error = FLO_NO_NONPOSITIVE_REAL;
								break;
							case NONNEGATIVE_REAL:
								if ($value<0)
									$this->error = FLO_NO_NONNEGATIVE_REAL;
								break;
						}
					}
					break;
				// end case NUMERIC
				
				// conditions: MIN_LENGTH, MAX_LENGTH, EXACT_LENGTH
				// parameters: no predefined parameters
				// user input: yes (integer)
				case MIN_LENGTH:
					if (strlen($value) < $this->parameter)
						$this->error = FLO_LOW_LENGTH.$this->parameter.FLO_CHARS;
					break;
				case MAX_LENGTH:
					if (strlen($value) > $this->parameter)
						$this->error = FLO_HIGH_LENGTH.$this->parameter.FLO_CHARS;
					break;
				case EXACT_LENGTH:
					if (strlen($value) != $this->parameter)
						$this->error = FLO_INVALID_LENGTH.$this->parameter.FLO_CHARS;
					break;
				
				// conditions: MIN_VALUE, MAX_VALUE
				// parameters: no predefined parameters
				// user input: yes (numeric)
				case MIN_VALUE:
					if ($value < $this->parameter)
						$this->error = FLO_LOW_VALUE;
					break;
				case MAX_VALUE:
					if ($value > $this->parameter)
						$this->error = FLO_HIGH_VALUE;
					break;
			} // end switch
		} // end if
		return ($this->error);
	}
} // end class Condition

?>
