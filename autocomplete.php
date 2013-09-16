<?php

/* Usage:

- Install jquery and jquery ui
- Copy this file wherever it suits you
- Insert a database connection
- Add the following script to your php file (below the flotilla form)
- optional parameters: label, order, limit
- Done!

<script>
	$(function() {
		$( "#YOUR_FIELD_ID" ).autocomplete({
			source: "autocomplete.php?table=YOUR_TABLE_NAME&field=YOUR_FIELD_NAME",
			appendTo: "#YOUR_FORM_ID"
		});
	});
</script>
*/

// uncomment these lines in development environment
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Datenbank-Verbindung erzeugen
require_once '../config/database_mysql.php';
require_once '../flotilla/lib/opendb_mysql.php';
$connection = openDB($db_host,$db_user,$db_pass,$db_name);

// Standardwerte
define (AC_DEFAULT_LIMIT, 5);

$table = isset($_GET['table'])?$_GET['table']:NULL;					// Tabellenauswahl (Pflicht)
$field = isset($_GET['field'])?$_GET['field']:NULL;					// Feldauswahl (Pflicht)
$term = isset($_GET['term'])?$_GET['term']:NULL;					// Benutzereingabe (wird von jQuery erzeugt)
$label = isset($_GET['label'])?$_GET['label']:NULL;					// Feldauswahl für Beschriftung (optional)
$order = isset($_GET['order'])?$_GET['order']:NULL;					// Sortierfeld (optional)
$limit = isset($_GET['limit'])?$_GET['limit']:AC_DEFAULT_LIMIT;		// Maximale Einträge (optional)

// Daten abfragen
if ($label) { 
	$querystring = "
		SELECT `$field` AS value, $label AS label
		FROM `$table`
		WHERE $label LIKE '%$term%'
		ORDER BY ".isset($order)?$order:'label'."
		LIMIT 0,$limit
	";
}
else {
	$querystring = "
		SELECT `$field` AS value, `$field` AS label
		FROM `$table`
		WHERE label LIKE '%$term%'
		ORDER BY ".isset($order)?$order:'label'."
		LIMIT 0,$limit
	";
}
$query = mysql_query ($querystring); 

// json-String erzeugen
$jsonList = '[ ';
$jsonStr = '';
while ($result = mysql_fetch_object($query)) {
	$jsonStr .= '{ "value":"'.$result->value.'"';
	if ($label) $jsonStr .= ', "label":"'.$result->label.'"';
	else $jsonStr .= ', "label":"'.$result->value.'"';
	$jsonStr .= ' },';
}
$jsonStr = rtrim($jsonStr, ',');
$jsonList .= $jsonStr." ]";

// und diesen ausgeben
header('Content-type: text/html; charset=utf-8');
echo $jsonList;

// dies hier dient nur Testzwecken
echo $querystring;

?>
