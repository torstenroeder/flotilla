<?php
class FieldCondition_excludedChars extends Field {

	public function Check ($value) {
		if (strpbrk($value,$this->parameters[0]))
			$this->error = 'Die Eingabe enthält ungültige Zeichen.';
		return ($this->error);
	}

}
?>
