<?php

class Action_DataFile extends Action {
	
	var $filename;
	var $separator;
	
	protected function __construct ($Creator, $filename, $separator = ACTION_DATAFILE_SEPARATOR) {
		$this->Creator = $Creator;
		if (is_file($filename)) {
			$this->filename = $filename;
		}
		else {
			$this->Creator->debuglog->Write(DEBUG_INFO,'. ACTION DATAFILE filename not specified');
			$script_noext = substr(basename($_SERVER['SCRIPT_FILENAME']),0,strrpos(basename($_SERVER['SCRIPT_FILENAME']),'.'));
			$this->filename = $script_noext.'.'.$this->Creator->name.'.data';
		}
		if (isset($separator)) $this->separator = $separator;
		$this->Creator->debuglog->Write(DEBUG_INFO,'. ACTION DATAFILE created');
	}
	
	static public function create () {
		// create ( Creator [, filename [, separator ]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new Action_DataFile ($args[0],NULL,NULL);
			case 2: return new Action_DataFile ($args[0],$args[1],NULL);
			case 3: return new Action_DataFile ($args[0],$args[1],$args[2]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. ACTION DATAFILE invalid number of arguments');
		}
	}
	
	public function onLoad () {
		$this->Creator->debuglog->Write(DEBUG_INFO,'. ACTION DATAFILE reading from '.$this->filename.'...');
		if (is_file($this->filename)) {
			$file = file($this->filename);
			reset($file);
			while (list($index, $row) = each($file)) {
				$separator = strpos($row,$this->separator);
				$field_name = trim(substr($row,0,$separator));
				$field_value = trim(substr($row,$separator+1));
				if (isset($this->Creator->Fields[$field_name])) {
					$this->Creator->Fields[$field_name]->user_value = $field_value;
					$this->Creator->debuglog->Write(DEBUG_INFO,". ACTION DATAFILE reading $field_name => $field_value");
				}
			}
		}
		else {
			$this->Creator->debuglog->Write(DEBUG_INFO,'. ACTION DATAFILE file not found, no data imported');
			$file = array();
		}
	}

	public function onSubmit () {
		$this->Creator->debuglog->Write(DEBUG_INFO,'Action_DataFile: writing to file');
		// Read contents from existing file
		$fields_in_file = array();
		if (is_file($this->filename)) {
			$file = file($this->filename);
			reset($file);
			while (list($index, $row) = each($file)) {
				if ($separator = strpos($row,$this->separator)) {
					$fields_in_file[$index] = trim(substr($row,0,$separator));
					//DebugInfo ("FIELD '".$fields_in_file[$index]."' found at line $index");
				}
			}
		}
		// Write contents to existing fields
		reset($this->Creator->Fields);
		while (list($index, $field) = each($this->Creator->Fields)) {
			$new_line = $field->name.$this->separator.$field->user_value.PHP_EOL;
			$line = array_search($field->name,$fields_in_file);
			if ($line !== false) {
				$this->Creator->debuglog->Write(DEBUG_INFO,". ACTION DATAFILE updating ... $field->name => $field->user_value");
				$file[$line] = $new_line;
			}
			else {
				$this->Creator->debuglog->Write(DEBUG_INFO,". ACTION DATAFILE inserting ... $field->name => $field->user_value");
				$file[] = $new_line;
			}
			file_put_contents($this->filename,$file);
		}
	}
	
}

?>
