<?php

namespace bitbetrieb\MVC\DatabaseHandler;

use bitbetrieb\MVC\Config\IConfig as IConfig;

/**
 * Class MySqlDatabaseHandler
 * @package bitbetrieb\MVC\DatabaseHandler
 */
class DatabaseHandler implements IDatabaseHandler {
    /**
     * Konfigurationsobjekt
     *
     * @var IConfig
     */
    private $config;

    /**
     * Verbindungsinformationen zur Datenbank
     *
     * @vars string
     */
    private $host;
    private $user;
    private $pass;
    private $name;

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
        $this->host = $this->config->get('database/host');
        $this->user = $this->config->get('database/user');
        $this->pass = $this->config->get('database/pass');
        $this->name = $this->config->get('database/name');

        $this->connect();
    }

    /**
     * Stelle Verbindung zur Datenbank her
     *
     * @throws \Exception Keine Konfiguration vorhanden. Daten der Verbindung können nicht gelesen werden.
     */
    public function connect() {
        $this->connection = new \PDO("mysql:dbname={$this->name};host={$this->host}", $this->user, $this->pass);
    }

    /**
     * Führe SQL Query aus und gebe ResultSet zurück
     *
     * @param IQueryObject $query
     *
     * @return QueryResult Objekt mit Erfolg und Daten
     */
    public function query(IQueryObject $query) {
        $result = new QueryResult();

        $this->checkConnection();

        $this->statement = $this->connection->query($query->assemble());

        if($this->checkStatement()) {
            $result->setSuccess(true);
            $result->setLastInsertId($this->connection->lastInsertId());
            $result->setData($this->getResultSet());
        }
        else {
            $result->setSuccess(false);
        }

        return $result;
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
    private function getResultSet() {
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}

?>
