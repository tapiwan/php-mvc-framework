<?php

namespace bitbetrieb\MVC\DatabaseHandler;

use bitbetrieb\MVC\Config\IConfig as IConfig;

/**
 * Interface IDatabaseHandler
 * @package bitbetrieb\MVC\DatabaseHandler
 */
interface IDatabaseHandler {
    public function __construct(IConfig $config);
    public function connect();
    public function query(IQueryObject $query);
}

?>
