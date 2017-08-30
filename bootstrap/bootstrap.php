<?php

require_once(FRAMEWORK_PATH."/framework/Autoloader/Autoloader.php");

use bitbetrieb\MVC\Autoloader\Autoloader;
use bitbetrieb\MVC\DependencyInjectionContainer\Container;

/*
|--------------------------------------------------------------------------
| Setup autoloader
|--------------------------------------------------------------------------
|
| We load the namespaces prefixes with their base directories
|
*/
$autoloader = new Autoloader();
$autoloader->load(FRAMEWORK_PATH."/config/autoload.php");

/*
|--------------------------------------------------------------------------
| Set dependency injection container
|--------------------------------------------------------------------------
|
| We set up the components of the DI container so we can use them globally
|
*/
Container::load(FRAMEWORK_PATH."/config/components.php");

/*
|--------------------------------------------------------------------------
| Start front controller
|--------------------------------------------------------------------------
|
| We start the front controller, by retrieving it from the DI container
| and executing it
|
*/
$frontController = Container::get('front-controller');
$frontController->load(FRAMEWORK_PATH."/app/routes.php");
$frontController->execute();

?>
