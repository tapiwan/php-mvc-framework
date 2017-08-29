<?php

namespace bitbetrieb\MVC\DatabaseHandler;

/**
 * Class MySqlDatabaseHandler
 * @package bitbetrieb\MVC\DatabaseHandler
 */
class DatabaseHandler implements IDatabaseHandler {
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
     * @param string $host
     * @param string $user
     * @param string $db
     * @param string $password
     */
    public function __construct($host, $user, $db, $password) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $db;
        $this->name = $password;

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
     * @param string $query
     *
     * @return QueryResult Objekt mit Erfolg und Daten
     */
    public function query($query) {
        $result = new QueryResult();

        $this->checkConnection();

        $this->statement = $this->connection->query($query);

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
