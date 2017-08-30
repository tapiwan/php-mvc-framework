<?php
/*
|--------------------------------------------------------------------------
| Define global variables
|--------------------------------------------------------------------------
|
| We define some global variables here so we have them available everywhere.
|
*/
define('FRAMEWORK_PATH', dirname(__DIR__));
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']);

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
|
| We require the bootstrap file
|
*/
require_once(FRAMEWORK_PATH."/bootstrap/bootstrap.php");

?>
