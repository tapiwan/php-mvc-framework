<?php

namespace bitbetrieb\CMS\DatabaseHandler;

use bitbetrieb\CMS\Config\IConfig as IConfig;

/**
 * Class MySqlDatabaseHandler
 * @package bitbetrieb\CMS\DatabaseHandler
 */
class DatabaseHandler implements IDatabaseHandler {
    /**
     * Konfigurationsobjekt
     *
     * @var IConfig|null
     */
    private $config = null;

    /**
     * Datenbankverbindung
     *
     * @var \PDO|null
     */
    private $connection = null;

    /**
     * Letzte Statement nach einem Query
     *
     * @var \PDOStatement|null
     */
    private $statement = null;

    /**
     * DatabaseHandler constructor.
     * @param IConfig $config
     */
    public function __construct(IConfig $config) {
        $this->config = $config;
        $this->connect();
    }

    /**
     * Stelle Verbindung zur Datenbank her
     *
     * @throws \Exception Keine Konfiguration vorhanden. Daten der Verbindung können nicht gelesen werden.
     */
    public function connect() {
        $this->checkConfig();

        $user = $this->config->get('db-user');
        $host = $this->config->get('db-host');
        $pass = $this->config->get('db-pass');
        $name = $this->config->get('db-name');

        $this->connection = new \PDO("mysql:dbname=$name;host=$host", $user, $pass);
    }

    /**
     * Führe SQL Query aus und gebe ResultSet zurück
     *
     * @param string $query SQL Query
     * $param boolean $execution_only Flag die entscheidet ob Query nur ausgeführt wird oder ob ein ResultSet zurückgegeben
     * werden soll
     *
     * @return array|boolean Assoziatives Array mit Schlüssel-Wert Paaren. False wenn Query nicht erfolgreich war. True
     * wenn Query erfolgreich war aber die $execution_only Flag benutzt wurde
     */
    public function query(IQueryObject $query) {
        $result = [];

        $this->checkConnection();

        $this->statement = $this->connection->query($query->assemble());

        if($this->checkStatement()) {
            foreach($this->getQueryResult() as $item) {
                $result[] = $item;
            };

            if(count($result) === 0) {
                $result = true;
            }
        }
        else {
            $result = false;
        }

        return $result;
    }

    /**
     * Überprüfen ob die Konfiguration geladen wurde
     *
     * @throws \Exception Keine Konfig
     */
    private function checkConfig() {
        if(is_null($this->config)) {
            throw new \Exception("Config missing, can't connect to database.");
        }
    }

    /**
     * Überprüfen ob eine Verbindung zur Datenbank besteht
     *
     * @throws \Exception Keine Verbindung zur Datenbank
     */
    private function checkConnection() {
        if(is_null($this->connection)) {
            throw new \Exception("Can't send query, no connection to database established.");
        }
    }

    /**
     * Überprüft ob der Query erfolgreich war
     *
     * @return bool
     */
    private function checkStatement() {
        if(!method_exists($this->statement, 'errorCode')) return false;
        if($this->statement->errorCode() !== "00000") return false;

        return true;
    }

    /**
     * Gibt ResultSet des Queries zurück
     *
     * @return array
     */
    private function getQueryResult() {
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}

?>
