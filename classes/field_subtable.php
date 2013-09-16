<?php

class Field_Subtable extends Field {
	
	public $subtable_name; // name of the target table
	public $subtable_key_column; // key column of the target table
	public $subtable_value_column; // value column of the target table
	public $separator = ', ';
	public $Relation;
	
	// CONSTRUCTORS -----------------------------------------------------------
	
	protected function __construct ($Creator, $name, $subtable_name, $subtable_value_column, $required) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->subtable_name = $subtable_name;
			$this->subtable_key_column = $name; // ! name = primary key column !
			$this->subtable_value_column = $subtable_value_column;
			$this->required = $required;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. new Subtable Field "'.$this->name.'" created');
		}
		else $this->Creator->debuglog->Write(DEBUG_ERROR,'. could not create new Subtable Field - name not specified');
	}
	
	static public function create() {
		// create ( $name, $subtable_name [, $subtable_value_column [, $required]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 3: return new Field_Subtable ($args[0],$args[1],$args[2],NULL,NULL);
			case 4: return new Field_Subtable ($args[0],$args[1],$args[2],$args[3],NULL);
			case 5: return new Field_Subtable ($args[0],$args[1],$args[2],$args[3],$args[4]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create new Subtable Field - invalid number of arguments');
		}
	}
	
	// SUBTABLE RELATION ------------------------------------------------------
	
	public function addRelation () {
		// for n:m relations
		// addRelation ( nm_table_name )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: $this->Relation = new Subtable_Relation ($args[0]); break;
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create Subtable n:m Relation - invalid number of arguments'); break;
		}
		return $this;
	}
	
	// HTML OUTPUT ------------------------------------------------------------
	
	public function HTMLOutput () {
		$output = NULL;
		if ((!isset($this->Relation)) && $this->Creator->Connection->getPrimaryKeyValue()) {
			// 1:n relation
			// when more than one column is given, separate their values by commas
			if (is_array($this->subtable_value_column))
				$fields = 'CONCAT('.implode(',\','.$separator.'\',',$this->subtable_value_column).')';
			else
				$fields = '`'.$this->subtable_value_column.'`';
			// perform query
			$relation_querystring = "
				SELECT $fields AS `value`, {$this->subtable_key_column} AS `key`
				FROM `{$this->subtable_name}` n
				WHERE n.`{$this->Creator->Connection->getPrimaryKeyName()}` = {$this->Creator->Connection->getPrimaryKeyValue()}
				ORDER BY `value`";
			if ($relation_query = mysql_query($relation_querystring)) {
				// fetch related row
				if ($row = mysql_fetch_object($relation_query)) {
					$output .= "\t\t\t".'<div>';
					//$output .= '<input type="checkbox" name="'.$this->name.'-remove[]" id="'.$this->getId().'-remove-'.$row_num.'" value="'.$row->key.'" title="'.FLO_REMOVE_SUBTABLE_FIELD.'" class="remove-subtable-checkbox"/>';
					//$output .= '<label for="'.$this->getId().'-remove-'.$row_num.'">'.$row->value.'</label>';
					$output .= $row->value;
					$output .= '</div>'.PHP_EOL;
					$row_num++;
				}
			}
			else {
				$this->Creator->debuglog->Write(DEBUG_WARNING,'. HTML OUTPUT: mysql error in '.$relation_querystring);
			}
		}
		elseif (isset($this->Relation) && $this->Creator->Connection->getPrimaryKeyValue()) {
			// n:m relation
			// when more than one column is given, separate their values by commas
			if (is_array($this->subtable_value_column))
				$fields = 'CONCAT(m.`'.implode('`,\','.$separator.'\',m.`',$this->subtable_value_column).'`)';
			else
				$fields = 'm.`'.$this->subtable_value_column.'`';
			// perform query
			$relation_querystring = "
				SELECT $fields AS `value`, `{$this->subtable_key_column}` AS `key`
				FROM `{$this->subtable_name}` m
				LEFT OUTER JOIN `{$this->Relation->table}` nm
					USING (`$this->subtable_key_column`)
				WHERE nm.`{$this->Creator->Connection->getPrimaryKeyName()}` = {$this->Creator->Connection->getPrimaryKeyValue()}
				ORDER BY `value`";
			if ($relation_query = mysql_query($relation_querystring)) {
				$row_num = 0;
				while ($row = mysql_fetch_object($relation_query)) {
					$output .= "\t\t\t".'<div>';
					$output .= '<input type="checkbox" name="'.$this->name.'-subtable[]" id="'.$this->getId().'-subtable-'.$row_num.'" value="'.$row->key.'" checked="checked" title="'.FLO_SUBTABLE_REMOVE_CHECKBOX.'" class="subtable-checkbox"/>';
					$output .= '<label for="'.$this->getId().'-subtable-'.$row_num.'">'.$row->value.'</label>';
					$output .= '</div>'.PHP_EOL;
					$row_num++;
				}
			}
			else {
				$this->Creator->debuglog->Write(DEBUG_WARNING,'. HTML OUTPUT: mysql error in '.$relation_querystring);
			}
		}
		// input field for new row
		$output .= "\t\t\t<input";
		$output .= " name=\"$this->name\"";
		$output .= " id=\"{$this->getId()}\"";
		$output .= ' type="text"';
		if ($this->language) $output .= ' lang="'.$this->language.'"';
		if ($this->direction) $output .= ' dir="'.$this->direction.'"';
		if ($this->length) $output .= ' maxlength="'.$this->length.'" size="'.$this->length.'"';
		$output .= ' value="'.htmlspecialchars(stripslashes($this->user_value)).'"';
		if ($this->is_not_hidden()) {
			$output .= $this->HTMLTitle();
			$output .= $this->HTMLClass();
			$output .= $this->HTMLStyle();
		}
		$output .= "/>".PHP_EOL;
		return $output;
	}
	
} // end class Field_Subtable

class Subtable_Relation {
	
	public $table;
	
	public function __construct ($table) {
		$this->table = $table;
	}
	
} // end class Subtable_Relation

?>
