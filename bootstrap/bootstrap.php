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
$container->initializeMapFromJSON("../config/services.json");


?>
