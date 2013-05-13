<?php

/*

Usage:
- Install jquery.
- Copy this file wherever it suits you.
- Insert a database connection.
- Add this snippet to your php file, below the flotilla form.
- Enjoy the fancy autocomplete.

<script>
	$(function() {
		$( "#YOUR_FIELD_ID" ).autocomplete({
			source: "autocomplete.php?table=YOUR_TABLE_NAME&field=YOUR_FIELD_NAME",
			appendTo: "#YOUR_FORM_ID"
		});
	});
</script>

*/

header('Content-type: text/html; charset=utf-8');

// Benutzereingabe
$table = $_GET['table'];
$field = $_GET['field'];
$input = $_GET['term'];
$label = isset($_GET['label'])?$_GET['label']:NULL;

// Suchstring der aktiven Datenansicht
$querystring = "SELECT `$field` AS value";
if ($label) $querystring .= ", `$label` AS label";
$querystring .= " FROM `$table` WHERE `$field` LIKE '%$input%' ORDER BY `$field` LIMIT 0,5";

$jsonList = '[ ';

// here you need to have created a database connection
$query = mysql_query ($querystring); 

$jsonStr = '';
while ($result = mysql_fetch_object($query)) {
	$jsonStr .= '{ "value":"'.$result->value.'"';
	if ($label) $jsonStr .= '{ "label":"'.$result->label.'"';
	$jsonStr .= ' },';
}
$jsonStr = rtrim($jsonStr, ',');
$jsonList .= $jsonStr." ]";
echo $jsonList;

?>
