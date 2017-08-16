<?php
/**
 * INCLUDES
 */
require_once(__DIR__."/../framework/Autoloader/Autoloader.php");

/**
 * IMPORTS
 */
use bitbetrieb\CMS\Autoloader\Autoloader as Autoloader;
use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;

/**
 * AUTOLOADER
 */
$autoloader = new Autoloader();
$autoloadList = file_get_contents("../config/autoload.json");
$autoloader->initializeViaJSON($autoloadList);

/**
 * DEPENDENCY INJECTION CONTAINER
 */
$containerList = file_get_contents("../config/container.json");
Container::initializeViaJSON($containerList);

/**
 * APPLICATION
 */
$application = Container::get('application');
$application->start();

?>
