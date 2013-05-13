<?php

class Field_Upload extends Field {

	var $temp_name;
	
	// CONSTRUCTORS -----------------------------------------------------------
	
	protected function __construct ($Creator, $name, $max_filesize = FLO_UPLOAD_FILESIZE, $filetype = NULL, $required = OPTIONAL, $target_dir = FLO_UPLOAD_DIR, $prefix = NULL) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->max_filesize = $max_filesize;
			$this->filetype = $filetype;
			$this->required = $required;
			$this->target_dir = $target_dir.'/';
			$this->prefix = $prefix;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. new Upload Field "'.$this->name.'" created');
		}
		else {
			$this->Creator->debuglog->Write(DEBUG_ERROR,'could not create new Upload Field - name not specified');
		}
	}
	
	static public function create() {
		// create ( upload_name [, max_filesize [, required [, target_directory ]]]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Field_Upload ($args[0],$args[1]);
			case 3: return new Field_Upload ($args[0],$args[1],$args[2]);
			case 4: return new Field_Upload ($args[0],$args[1],$args[2],$args[3]);
			case 5: return new Field_Upload ($args[0],$args[1],$args[2],$args[3],$args[4]);
			case 6: return new Field_Upload ($args[0],$args[1],$args[2],$args[3],$args[4],$args[5]);
			case 7: return new Field_Upload ($args[0],$args[1],$args[2],$args[3],$args[4],$args[5],$args[6]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create new Upload Field - invalid number of arguments');
		}
	}
	
	// EVALUATION -------------------------------------------------------------
	
	public function EvaluatePost () {
		$this->error = NULL;
		// Datei
		$this->Creator->debuglog->Write(DEBUG_INFO,". checking $this->name");
		switch ($_FILES[$this->name]['error']) {
			case UPLOAD_ERR_OK:
				$this->Creator->debuglog->Write(DEBUG_INFO,". . file '$this->name' submitted: ".$_FILES[$this->name]['name']);
				// CHECK TYPE
				if ($this->filetype) {
					if (stripos($this->filetype,$_FILES[$this->name]['type']) === false && stripos($_FILES[$this->name]['type'],$this->filetype) === false) {
						$this->error = '"'.$_FILES[$this->name]['type'].'"'.FLO_UPLOAD_ERR_FILETYPE.'"'.$this->filetype.'".';
					}
				}
				// CHECK SIZE
				if ($_FILES[$this->name]['size'] > $this->max_filesize*1024) {
					$this->error = FLO_UPLOAD_ERR_FILESIZE.$this->max_filesize.' KB.';
				}
				if (is_null($this->error)) {
					if ($this->prefix) $this->prefix = preg_replace('/(\{(\w*)\})/e',"\$this->Creator->Fields['$2']->user_value",$this->prefix);
					$this->user_value = ($this->prefix?$this->prefix.'_':'').$_FILES[$this->name]['name'];
					$destination = $this->target_dir.$this->user_value;
					if (move_uploaded_file($_FILES[$this->name]['tmp_name'],$destination)) {
						// SUCCESS
						$this->Creator->debuglog->Write(DEBUG_INFO,'. file was uploaded successfully');
						//$this->user_value = '';
						$this->valid = true;
					}
					else {
						// SOME STRANGE ERROR
						$this->Creator->debuglog->Write(DEBUG_WARNING,'file was not uploaded to '.$destination.' - some error occurred');
						$this->error = FLO_UPLOAD_ERR_MOVE;
					}
				}
				if ($this->error) {
					// if a valid file was submitted before, everything is fine
					if ($_POST[$this->name.FLO_UPLOAD_FIELD_SUFFIX] != '') {
						$this->user_value = $_POST[$this->name.FLO_UPLOAD_FIELD_SUFFIX];
						$this->Creator->debuglog->Write(DEBUG_INFO,". . file '$this->name' was submitted before");
						$this->error = NULL;
						$this->valid = true;
					}
				}
				break;
			case UPLOAD_ERR_INI_SIZE:
				$this->error = FLO_UPLOAD_ERR_INI_SIZE;
				break;
			case UPLOAD_ERR_PARTIAL:
				$this->error = FLO_UPLOAD_ERR_PARTIAL;
				break;
			case UPLOAD_ERR_NO_FILE:
				$this->Creator->debuglog->Write(DEBUG_INFO,". . no file submitted for '$this->name'");
				if ($_POST[$this->name.FLO_UPLOAD_FIELD_SUFFIX] != '' && !isset($_POST[$this->name.'-remove'])) {
					$this->user_value = $_POST[$this->name.FLO_UPLOAD_FIELD_SUFFIX];
					$this->Creator->debuglog->Write(DEBUG_INFO,". . file '$this->name' was submitted before");
					$this->valid = true;
				} elseif ($this->required) {
					$this->error = FLO_UPLOAD_ERR_NO_FILE;
					$this->Creator->debuglog->Write(DEBUG_INFO,". . file '$this->name' is required");
				} else {
					$this->Creator->debuglog->Write(DEBUG_INFO,". . file '$this->name' is optional");
					$this->valid = true;
				}
				break;
		}
		return ($this->error == NULL);
	}

	// HTML OUTPUT ------------------------------------------------------------

	public function HTMLOutput () {
		$output = NULL;
		if ($this->user_value) {
			$output .= "\t\t\t<div>".PHP_EOL;
			$output .= "\t\t\t\t".'<a href="'.$this->target_dir.$this->user_value.'" target="_blank">'.PHP_EOL;
			$output .= "\t\t\t\t".'<input name="'.$this->name.'-remove" id="'.$this->getId().'-remove" type="checkbox" value="1" class="remove-upload-checkbox" title="'.FLO_FILE_REMOVE_CHECKBOX.'"/>'.PHP_EOL;
			$output .= "\t\t\t\t".'<input name="'.$this->name.FLO_UPLOAD_FIELD_SUFFIX.'" type="readonly" class="file" value="'.$this->user_value.'"/>'.PHP_EOL;
			$output .= "\t\t\t\t".'</a>'.PHP_EOL;
			$output .= "\t\t\t\t".'<label for="'.$this->getId().'-remove">'.FLO_FILE_REMOVE.'</label>'.PHP_EOL;
		}
		else {
			$output .= "\t\t\t\t".'<input name="'.$this->name.FLO_UPLOAD_FIELD_SUFFIX.'" type="hidden" value="'.$this->user_value.'"/>'.PHP_EOL;
		}
		$output .= "\t\t\t</div>".PHP_EOL;
		$output .= "\t\t\t<div>".PHP_EOL;
		$output .= "\t\t\t\t".'<input name="'.$this->name.'" id="'.$this->getId().'" type="file"';
		$output .= $this->HTMLTitle();
		$output .= $this->HTMLClass();
		$output .= $this->HTMLStyle();
		$output .= ' onchange="document.getElementById(\''.$this->getId().'-filename\').value = this.value"/>'.PHP_EOL;
		$output .= "\t\t\t\t".'<input name="'.$this->name.'-filename" id="'.$this->getId().'-filename" type="readonly" onclick="document.getElementById(\''.$this->getId().'\').click()"/>'.PHP_EOL;
		$output .= "\t\t\t\t".'<label for="'.$this->getId().'" onclick="document.getElementById(\''.$this->getId().'\').click()">'.FLO_FILE_SELECT.'</label>'.PHP_EOL;
		$output .= "\t\t\t</div>".PHP_EOL;
		return $output;
	}

} // end class Field_Upload

?>
