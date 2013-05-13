<?php

function openDB($db_host,$db_user,$db_pass,$db_name) {
	if (!$link = @mysql_connect($db_host,$db_user,$db_pass,$db_name)) {
		die ('MYSQL : connection failed');
		return false;
	} else {
		// connection was established;
		mysql_select_db($db_name);
		if (!function_exists('mysql_set_charset')) {
			// creating charset function
			function mysql_set_charset($charset,$link) {
				return mysql_query("SET NAMES $charset",$link);
			}
		}
		// charset
		mysql_set_charset('utf8',$link);
		return $link;
	}
}

?>
