<?php
session_start();
require_once('../classes/'.$_SESSION['flotilla']['connection'][0]);

openDB($_SESSION['flotilla']['connection'][1],
       $_SESSION['flotilla']['connection'][2],
       $_SESSION['flotilla']['connection'][3],
       $_SESSION['flotilla']['connection'][4]);

$relation = $_SESSION['flotilla']['subtables'][$_GET['table']];
$subtable_name = $relation[name];
$subtable_key = $relation[key];
$subtable_value = $relation[value];
$subtable_initial = empty($_GET['initial'])?0:$_GET['initial'];

if (is_array($subtable_value)) {
  $subtable_value = 'CONCAT('.implode(',\', \',',$subtable_value).')';
}

$initials_querystring = "SELECT UCASE(SUBSTRING($subtable_value,1,1)) as initial FROM $subtable_name GROUP BY initial ORDER BY initial";
$initials_query = mysql_query($initials_querystring);

$html .= '<p>';
$count = 0;
while ($initial = mysql_fetch_object($initials_query)) {
  $count++;
  $html .= "<a href=\"?table=$_GET[table]&initial=$initial->initial\">$initial->initial</a> ";
  if ($count % 15 == 0) $html .= '<br>';
}
$html .= '</p>';
if ($subtable_initial) {
  $html .= '<p>';
  $querystring = "SELECT $subtable_key,$subtable_value FROM $subtable_name WHERE $subtable_value LIKE '$subtable_initial%' ORDER BY $subtable_value";
  $query = mysql_query($querystring);
  $html .= "<select id=\"FlotillaSubtable\" onchange=\"putSelection('$subtable_name');\">";  if ($subtable_initial) $html .= "<option value=\"\">$subtable_initial ...";
  else $html .= '<option value="">Bitte auswählen ...'; 
  while ($row = mysql_fetch_object($query)) {
    $html .= "<option value=\"{$row->$subtable_value}\">{$row->$subtable_value}";
  }
  $html .= '</select>';
  $html .= '</p>';
}

// template

$template_title = 'Auswahlliste';
$template_content = $html;

require_once 'subtable.tpl.php';

//print_r($_SESSION['flotilla']);
?>
