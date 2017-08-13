<?php
/**
 * INCLUDES
 */
include realpath("framework/CLI/CLI.php");

/**
 * IMPORTS
 */
use bitbetrieb\CMS\CLI\CLI as CLI;

/**
 * COMMAND LINE INTERFACE
 */
$cli = new CLI($argv, __DIR__);
$cli->start();

?>

