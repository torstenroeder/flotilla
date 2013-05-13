<?php

// Flotilla
// last known update: 2012-12-24

// PHP DEBUGGING INFO

// uncomment this line in productive environment
//ini_set('display_errors', 0);
//error_reporting(0);

// uncomment this line in development environment
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DEBUGGER
require_once 'lib/debuglog.php';

// LOAD CONFIGURATION
require_once 'config/flotilla.default.config.php';

// TRANSLATIONS
require_once 'translations/flotilla.dictionary.'.FLO_LANGUAGE.'.php';

// DICTIONARIES (CONTAINS CONSTANTS FOR USER)
require_once 'classes/form.dict.php';
require_once 'classes/field.dict.php';
require_once 'classes/field-condition.dict.php';
require_once 'classes/button.dict.php';
require_once 'classes/connection.dict.php';
require_once 'classes/action.dict.php';

// LOAD CLASS
require_once 'classes/form.php';

?>
