<?php

namespace bitbetrieb\CMS\DatabaseHandler;

use bitbetrieb\CMS\Config\IConfig as IConfig;

class MySqlDatabaseHandler implements IDatabaseHandler {
    private $config = null;
    private $connection = null;

    public function __construct(IConfig $config) {
        $this->config = $config;
        $this->connect();
    }

    public function connect() {
        if(is_null($this->config)) {
            throw new \Exception("Config missing, can't connect to database.");
        }

        $user = $this->config->get('db-user');
        $host = $this->config->get('db-host');
        $pass = $this->config->get('db-pass');
        $name = $this->config->get('db-name');

       // $this->connection = new \PDO("mysql:dbname=$name;host=$host", $user, $pass);
    }

    public function query($query, $args) {
        if(is_null($this->connection)) {
            throw new \Exception("Can't send query, no connection to database established.");
        }

        /*$stmt = $this->connection->prepare($query);
        $stmt->execute($args);
        return $stmt;*/

        echo $query;
    }
}

?>
