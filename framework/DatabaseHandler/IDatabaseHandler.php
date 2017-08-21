<?php

namespace bitbetrieb\CMS\DatabaseHandler;

use bitbetrieb\CMS\Config\IConfig as IConfig;

/**
 * Interface IDatabaseHandler
 * @package bitbetrieb\CMS\DatabaseHandler
 */
interface IDatabaseHandler {
    public function __construct(IConfig $config);
    public function connect();
    public function query(IQueryObject $query);
}

?>
