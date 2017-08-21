<?php

require_once(APP_PATH."/framework/Autoloader/Autoloader.php");

use bitbetrieb\CMS\Autoloader\Autoloader as Autoloader;
use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;

/*
|--------------------------------------------------------------------------
| Setup autoloader
|--------------------------------------------------------------------------
|
| We load the namespaces prefixes with their base directories from an external json file
|
*/
$autoloader = new Autoloader();
$autoloadList = file_get_contents(APP_PATH."/config/autoload.json");
$autoloader->initializeViaJSON($autoloadList);

/*
|--------------------------------------------------------------------------
| Set dependency injection container
|--------------------------------------------------------------------------
|
| We set up the dependencies of the DI container so we can use them globally
|
*/
$containerList = file_get_contents(APP_PATH."/config/container.json");
Container::initializeViaJSON($containerList);


/*
|--------------------------------------------------------------------------
| Start application
|--------------------------------------------------------------------------
|
| We start the application, by retrieving it from the DI container
|
*/
$application = Container::get('application');
$application->start();

?>
