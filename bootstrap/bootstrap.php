<?php
/*
|--------------------------------------------------------------------------
| Define global variables
|--------------------------------------------------------------------------
|
| We define some global variables here so we have them available everywhere.
|
*/
define('APP_PATH', dirname(__DIR__));
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']);

/*
|--------------------------------------------------------------------------
| Include the autoloader
|--------------------------------------------------------------------------
|
| Require the PSR-4 autoloader so that we can load all of our components without needing
| to include them manually.
|
*/
require_once (APP_PATH."/framework/Autoloader/Autoloader.php");

/**
 * NAMESPACES
 */
use bitbetrieb\CMS\Autoloader\Autoloader as Autoloader;
use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;

/**
 * SETUP AUTOLOADER
 */
$autoloader = new Autoloader();
$autoloadList = file_get_contents(APP_PATH."/config/autoload.json");
$autoloader->initializeViaJSON($autoloadList);

/**
 * DEPENDENCY INJECTION CONTAINER
 */
$container = new Container();
$containerList = file_get_contents(APP_PATH."../config/container.json");
$container->initializeViaJSON($containerList);


/**
 * APPLICATION
 */
$application = $container->get('application');
$application->start();

?>
