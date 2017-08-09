<?php

/**
 * AUTOLOADER
 */
include realpath("../framework/Autoloader/Autoloader.php");
$autoloader = new bitbetrieb\CMS\Autoloader\Autoloader();
$autoloader->initializeNamespacesFromJSON("../config/autoload.json");

/**
 * DEPENDENCY INJECTION CONTAINER
 */
$container = new bitbetrieb\CMS\DependencyInjectionContainer\Container();
$container->initializeMapFromJSON("../config/dependencies.json");

/**
 * APPLICATION
 */
$application = $container->get('application');

print_r($application);


?>
