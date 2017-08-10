<?php
include realpath("../framework/Autoloader/Autoloader.php");
use bitbetrieb\CMS\Autoloader\Autoloader as Autoloader;
use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;

/**
 * AUTOLOADER
 */
$autoloadList = file_get_contents(realpath("../config/autoload.json"));
Autoloader::initializeViaJSON($autoloadList);

/**
 * DEPENDENCY INJECTION CONTAINER
 */
$dependencyList = file_get_contents(realpath("../config/dependencies.json"));
Container::initializeViaJSON($dependencyList);

/**
 * APPLICATION
 */
$application = Container::get('application');

print_r(print_r($application));


?>
