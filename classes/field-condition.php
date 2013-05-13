<?php

// CLASS DEFINITION ------------------------------------------------------------

abstract class FieldCondition {
	
	public $parameters; // this would be an array
	public $Creator;
	
	protected $error;
	
	public function __construct ($args) {
		$this->Creator = array_shift($args);
		if (isset($args)) $this->parameters = $args;
	}
	
} // end class Condition

?>
