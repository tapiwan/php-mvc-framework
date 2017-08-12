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
     * @var IConfig
     */
    private $config;

    /**
     * Datenbankverbindung
     *
     * @var \PDO|null
     */
    private $connection = null;

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
        if(is_null($this->config)) {
            throw new \Exception("Config missing, can't connect to database.");
        }

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
     *
     * @throws \Exception Keine Verbindung zur Datenbank
     */
    public function query($query, $execution_only = false) {
        $data = [];

        //Überprüfen ob Verbindung zur Datenbank besteht
        if(is_null($this->connection)) {
            throw new \Exception("Can't send query, no connection to database established.");
        }

        //Query ausführen
        $stmt = $this->connection->query($query);

        //Query war nicht erfolgreich
        if(!method_exists($stmt, 'errorCode')) return false;
        if($stmt->errorCode() !== "00000") return false;

        //Überprüfen ob ResultSet zurückgegeben werden soll oder nur Information über Erfolg
        if($execution_only) {
            $data = true;
        }
        else {
            //Befülle DataSet;
            foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $result) {
                $data[] = $result;
            };
        }

        return $data;
    }
}

?>
