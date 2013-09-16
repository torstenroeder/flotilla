<?php

class Action_Database extends Action {
	
	protected $numeric_vartypes = array('int','tinyint','smallint','mediumint','longint','bigint');
	protected $string_vartypes = array('char','varchar','text','date','time');
	
	private $table;
	private $autoinsert;
	
	// CONSTRUCTORS --------------------------------------------------------------
	
	protected function __construct ($Creator,$table,$autoinsert = AUTOINSERT_OFF) {
		$this->Creator = $Creator;
		$this->table = $table;
		$this->autoinsert = $autoinsert;
		$this->Creator->debuglog->Write(DEBUG_INFO,'. DATABASE ACTION created');
	}
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Action_Database ($args[0],$args[1]);
			case 3: return new Action_Database ($args[0],$args[1],$args[2]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. DATABASE ACTION - invalid number of arguments');
		}
	}
	
	// QUERIES -------------------------------------------------------------------
	
	function Query ($querystring) {
		$result = mysql_query($querystring);
		if (!$result) $this->Creator->debuglog->Write(DEBUG_ERROR,'. mysql> '.$querystring);
		else $this->Creator->debuglog->Write(DEBUG_INFO,'. mysql> '.$querystring);
		return $result;
	}
	
	function Query_Select () {
		$querystring = 'SELECT * FROM `'.$this->table.'`';
		$querystring .= ' WHERE `'.$this->Creator->Connection->GetPrimaryKeyName().'`='.$this->Creator->Connection->GetPrimaryKeyValue();
		return $this->Query($querystring);
	}
	
	function Query_Insert () {
		$querystring = 'INSERT INTO `'.$this->table.'`';
		$insert_names = array();
		$insert_values = array();
		reset($this->Creator->Fields);
		while (list($index, $Field) = each($this->Creator->Fields)) {
			// all field values except subtable references will be inserted
			if ($Field->name != $this->Creator->Connection->GetPrimaryKeyName()) {
				switch (get_class($Field)) {
					case 'Field_Upload':
						$insert_values[] = '\''.$Field->user_value.'\'';
						$this->Creator->debuglog->Write(DEBUG_INFO,'. preparing upload field: '.$Field->name.' = '.$Field->user_value);
						$insert_names[] = '`'.$Field->name.'`';
						break;
					case 'Field_Subtable':
						break;
					case 'Field_StaticText':
						break;
					default:
						// check for numeric fields
						$column_qs = "SHOW COLUMNS FROM `$this->table` LIKE '$Field->name'";
						$column_info = mysql_fetch_object(mysql_query($column_qs));
						$column_type = preg_replace ('#(\(.+\))? ?(unsigned)?#','',$column_info->Type);
						if (in_array($column_type,$this->numeric_vartypes)) {
							// insert numeric data
							if ($Field->user_value == '')
								$insert_values[] = 0;
							else
								$insert_values[] = $Field->user_value;
							$this->Creator->debuglog->Write(DEBUG_INFO,'. preparing numeric field: '.$Field->name.' = '.$Field->user_value);
						}
						else {
							// insert textual field
							$insert_values[] = '\''.addslashes($Field->user_value).'\'';
							$this->Creator->debuglog->Write(DEBUG_INFO,'. preparing textual field: '.$Field->name.' = '.$Field->user_value);
						}
						$insert_names[] = '`'.$Field->name.'`';
						break;
				}
			}
		}
		$querystring .= ' ('.(implode(',',$insert_names)).')';
		$querystring .= ' VALUES ('.(implode(',',$insert_values)).')';
		$query = $this->Query($querystring);
		$new_id = mysql_insert_id();
		$this->Creator->Connection->setPrimaryKeyValue($new_id);
		$this->Creator->debuglog->Write(DEBUG_INFO,'. new ID: '.$new_id);
		return $query;
	}
	
	function Query_Update () {
		$querystring = 'UPDATE `'.$this->table.'`';
		$update_values = array();
		reset($this->Creator->Fields);
		while (list($index, $Field) = each($this->Creator->Fields)) {
			// all fields except subtable references will be updated
			if ($Field->name != $this->Creator->Connection->GetPrimaryKeyName()) {
				switch (get_class($Field)) {
					case 'Field_Upload':
						$update_values[] = '`'.$Field->name.'`=\''.addslashes($Field->user_value).'\'';
						$this->Creator->debuglog->Write(DEBUG_INFO,'. preparing upload field: '.$Field->name.' = '.$Field->user_value);
						break;
					case 'Field_Subtable':
						break;
					case 'Field_StaticText':
						break;
					default:
						// check for numeric fields
						$column_qs = "SHOW COLUMNS FROM `$this->table` LIKE '$Field->name'";
						$column_info = mysql_fetch_object(mysql_query($column_qs));
						$column_type = preg_replace ('#(\(.+\))? ?(unsigned)?#','',$column_info->Type);
						if (in_array($column_type,$this->numeric_vartypes)) {
							// update numeric field
							if ($Field->user_value == '')
								$update_values[] = '`'.$Field->name.'`=0';
							else
								$update_values[] = '`'.$Field->name.'`='.$Field->user_value;
							$this->Creator->debuglog->Write(DEBUG_INFO,'. preparing numeric field: '.$Field->name.' = '.$Field->user_value);
						}
						else {
							// update textual field
							$update_values[] = '`'.$Field->name.'`=\''.addslashes($Field->user_value).'\'';
							$this->Creator->debuglog->Write(DEBUG_INFO,'. preparing textual field: '.$Field->name.' = '.$Field->user_value);
						}
						break;
				}
			}
		}
		$querystring .= ' SET '.(implode(',',$update_values));
		$querystring .= ' WHERE `'.$this->Creator->Connection->GetPrimaryKeyName().'`='.$this->Creator->Connection->GetPrimaryKeyValue();
		$query = $this->Query($querystring);
		return $query;
	}
	
	/*
		if (isset($_POST[$Field->name.'-subtable'])) {
			// retrieve all entries stored in the database
			$subtable_querystring = "
				SELECT
					`{$Field->Creator->Connection->getPrimaryKeyName()}` as n,
					`{$Field->name}` as m
				FROM `{$Field->Relation->table}`
				WHERE `{$Field->Creator->Connection->getPrimaryKeyName()}` = {$Field->Creator->Connection->getPrimaryKeyValue()}
			";
			$subtable_query = mysql_query($subtable_querystring);
			$subtable = array();
			// create an array for the entries to be deleted
			while ($row = mysql_fetch_row($subtable_query)) {
				$subtable[$row[1]] = $row[0];
			}
			// remove fields with a check
			foreach ($_POST[$Field->name.'-subtable'] as $key => $value) {
				if (isset($subtable[$value])) {
					unset($subtable[$value]);
				}
			}
			foreach ($subtable as $row) {
				$remove_querystring = "
					DELETE
					FROM `{$Field->Relation->table}`
					WHERE {$Field->Creator->Connection->getPrimaryKeyName()} = {$Field->Creator->Connection->getPrimaryKeyValue()}
						AND $Field->subtable_key_column = $remove_value
					LIMIT 1
				";
				mysql_query($remove_querystring);
				$this->Creator->debuglog->Write(DEBUG_INFO,'. removing subtable fields: '.implode(',',$_POST[$Field->name.'-remove']));
			}
		}
	*/
	
	function Query_Update_Subtables () {
		reset($this->Creator->Fields);
		while (list($index, $Field) = each($this->Creator->Fields)) {
			if ( (get_class($Field) == 'Field_Subtable') && (is_array($Field->user_value)) ) {
				// check if the value already exists in subtable
				if (is_array($Field->subtable_value_column)) {
					// array: multi value field
					$fields = 'CONCAT('.implode(',\', \',',$Field->subtable_value_column).')';
					$this->Creator->debuglog->Write(DEBUG_INFO,'. SUBTABLE FIELD found ('.implode(',',$Field->subtable_value_column).')');
				}
				else {
					// single value field
					$fields = '`'.$Field->subtable_value_column.'`';
					$this->Creator->debuglog->Write(DEBUG_INFO,'. SUBTABLE FIELD found ('.$Field->subtable_value_column.')');
				}
				$subtable_check_querystring = "
					SELECT `$Field->subtable_key_column`
					FROM `$Field->subtable_name`
					WHERE $fields='".addslashes($Field->user_value)."'
				";
				$subtable_check_query = mysql_query ($subtable_check_querystring);
				if (mysql_num_rows($subtable_check_query)==0) {
					// if value does not exist, check whether autoinsert is enabled
					if ($this->autoinsert) {
						// create a new entry in the subtable
						if (is_array($Field->subtable_value_column)) {
							// multiple fields
							$this->Creator->debuglog->Write(DEBUG_INFO,'. TRY to insert new values');
							$values = explode(',',$Field->user_value);
							$values_array = array();
							$fields_array = array();
							while (list($index, $val) = each($values)) {
								$values_array[$index] = addslashes(trim($val));
								$fields_array[$index] = $Field->subtable_value_column[$index];
							}
							$values = implode('\',\'',$values_array);
							$fields = implode('`,`',$fields_array);
							$subtable_insert_querystring = "INSERT INTO `$Field->subtable_name` (`$fields`) VALUES ('".$values."')";
						}
						else {
							// single field
							$this->Creator->debuglog->Write(DEBUG_INFO,'. TRY to insert new value');
							$subtable_insert_querystring = "INSERT INTO `$Field->subtable_name` (`$Field->subtable_value_column`) VALUES ('".addslashes($Field->user_value)."')";
						}
						$subtable_insert_query = $this->Query($subtable_insert_querystring);
						// get the new id
						$new_id = mysql_insert_id();
					}
					else {
						// do not create a new entry
						$Field->error = FLO_ERR_SUBTABLE_INSERT;
						$Field->valid = false;
					}
				}
				else {
					// if value exists, look up the corresponding id
					$this->Creator->debuglog->Write(DEBUG_INFO,'. reference field found');
					$row = mysql_fetch_object($subtable_check_query);
					$new_id = $row->{$Field->subtable_key_column};
				}
				// now update the relation table
				if (isset($new_id)) {
					$relation_insert_querystring = "
						INSERT INTO `{$Field->Relation->table}` ($Field->subtable_key_column,{$Field->Creator->Connection->getPrimaryKeyName()})
						VALUES ($new_id,{$Field->Creator->Connection->getPrimaryKeyValue()})
					";
					$Field->user_value = NULL;
					$relation_insert_query = $this->Query($relation_insert_querystring);
				}
			}
		}
		return true;
	}
	
	function Query_Delete () {
		$querystring = 'DELETE FROM '.$this->table;
		$querystring .= ' WHERE '.$this->Creator->Connection->GetPrimaryKeyName().'='.$this->Creator->Connection->GetPrimaryKeyValue();
		return $this->Query($querystring);
	}
	
	// DATA INPUT / OUTPUT -------------------------------------------------------
	
	function onLoad () {
		if ($this->Creator->Connection->getPrimaryKeyValue()) {
			$this->Creator->debuglog->Write(DEBUG_INFO,'PRIMARY KEY found');
			if ($query = $this->Query_Select()) {
				$this->Creator->debuglog->Write(DEBUG_INFO,'SELECT successful');
				$db_fields = mysql_fetch_array($query);
				reset($db_fields);
				while (list($field_name, $field_value) = each($db_fields)) {
					if (isset($this->Creator->Fields[$field_name])
						&& $this->Creator->Fields[$field_name]->is_not_hidden()) {
						$this->Creator->Fields[$field_name]->user_value = $field_value;
						$this->Creator->debuglog->Write(DEBUG_INFO,'. VALUE imported: '.$field_name.' = '.$field_value);
					}
				}
			} else {
				$this->Creator->debuglog->Write(DEBUG_ERROR,'SELECT failed');
			}
		}
		else {
			$this->Creator->debuglog->Write(DEBUG_INFO,'PRIMARY KEY not found');
		}
	}
	
	function onSubmit () {
		$this->Creator->debuglog->Write(DEBUG_INFO,'DATABASE ACTION');
		if ($this->Creator->Connection->getPrimaryKeyValue()) {
			// UPDATE EXISTING ENTRY
			$this->Creator->debuglog->Write(DEBUG_INFO,'. PRIMARY KEY found');
			if (($this->Query_Update()) && ($this->Query_Update_Subtables())) {
				$this->Creator->debuglog->Write(DEBUG_INFO,'. UPDATE successful');
			} else {
				$this->Creator->debuglog->Write(DEBUG_ERROR,'UPDATE failed');
			}
		}
		else {
			// INSERT NEW ENTRY
			$this->Creator->debuglog->Write(DEBUG_INFO,'. PRIMARY KEY not found');
			if (($this->Query_Insert()) && ($this->Query_Update_Subtables())) {
				$this->Creator->debuglog->Write(DEBUG_INFO,'. INSERT successful');
			} else {
				$this->Creator->debuglog->Write(DEBUG_ERROR,'INSERT failed');
			}
		}
	}

}

?>
