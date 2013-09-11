<?php

// FLOTILLA CONFIGURATION FILE

// GENERAL SETTINGS ------------------------------------------------------------

// button labels
define('DEFAULT_LABEL',NULL);
define('NO_LABEL','');

// imagebuttons
define('DEFAULT_IMAGE',NULL);
define('NO_IMAGE','');

// debug log
define ('DEBUG_LEVEL',4);

// INDIVUDUAL SETTINGS ---------------------------------------------------------

// interface language
define('FLO_LANGUAGE',					'en');

// automatic field width
define('FLO_AUTO_FIELD_WIDTH',			true);
define('FLO_AUTO_FIELD_WIDTH_FACTOR',	6);
define('FLO_AUTO_FIELD_WIDTH_MIN',		10);
define('FLO_AUTO_FIELD_WIDTH_MAX',		300);

// form layout
define ('FLO_MULTISELECT_MAX_AUTOSIZE',	10);
define ('FLO_TEXTAREA_ROWS',			5);
define ('FLO_FIELD_LENGTH',				50);

// imagebuttons
define('FLO_IMAGEBUTTON_PATH',			'flotilla/icons/fugue/buttons/');
define('FLO_IMAGEBUTTON_EXTENSION',		'png');
define('FLO_IMAGEBUTTON_POSITION',		'left');

// textfile format
define('FLO_DATAFILE_SEPARATOR',		'=');

// upload field
define('FLO_UPLOAD_DIR',				'files');
define('FLO_UPLOAD_FIELD_SUFFIX',		'_filename');
define('FLO_UPLOAD_FILESIZE',			256); // kilobytes
//define('DEFAULT_DIR',					NULL);

?>
