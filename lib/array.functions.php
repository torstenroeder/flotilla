<?php
function array_to_string($array, $delimiter = NULL, $last_delimiter = NULL, $skip_empty_values = true)
{
	if (!$last_delimiter) $last_delimiter = $delimiter;
	$string = NULL;
	if ($skip_empty_values) {
		// remove empty values from array
		reset($array);
		while(list($key,$value) = each($array)) {
			if ($value == '') unset($array[$key]);
		}
	}
	$arlength = count($array);
	$arkeys = array_keys($array);
	for($i=0; $i<$arlength; $i++) {
		$string .= $array[$arkeys[$i]];
		if ($i < $arlength-2) $string .= $delimiter;
		if ($i == $arlength-2) $string .= $last_delimiter;
	}
	return $string;
}

// test

// one delimiter:
//echo array_to_string (array('surname','forename'),', '); // surname, forename

// skip empty values:
//echo array_to_string (array('surname',''),', '); // surname
//echo array_to_string (array('1','2','3','','5','6'),', '); // 1, 2, 3, 5, 6
//echo array_to_string (array('1','2','3','','5','6'),', ',', ',false); // 1, 2, 3, , 5, 6

// individual last delimiter:
//echo array_to_string (array('a','b','c'),', ',' and '); // a, b and c
//echo array_to_string (array('a','b'),', ',' and '); // a and b

// individual last delimiter with skip:
//echo array_to_string (array('a','b','','d'),', ',' and '); // a, b and d

?>