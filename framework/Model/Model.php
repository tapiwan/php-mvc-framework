<?php

namespace bitbetrieb\CMS\Model;

use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;

/**
 * Class Model
 * @package bitbetrieb\CMS\Model
 */
abstract class Model {
    /**
     * Zum Model zugehöriger Tabellenname
     * Wird automatisch erzeugt, kann aber überschrieben werden
     *
     * @var string
     */
    protected $table;

    /**
     * Assoziatives Array mit Daten des Models
     * Wird automatisch befüllt.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Primärschlüssel
     */
    protected $primaryKey = 'id';

    /**
     * Spaltennamen der dem Model zugehörigen Tabelle
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Zeitstempel
     */
    protected $updatedAt = 'updated_at';
    protected $createdAt = 'created_at';

    /**
     * Spalten welche nicht als JSON ausgegeben werden sollen
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Database Handler des Models
     *
     * @var object
     */
    protected $dbh;

    /**
     * Model constructor.
     */
    public function __construct($data = null) {
        $this->table = $this->getDefaultTableName();
        $this->dbh = Container::get('database-handler');

        $this->load($data);
    }

    /**
     * Gib Datum des Models anhand von einem Schlüssel aus, insofern es existiert
     *
     * @param string $key Schlüssel
     *
     * @return mixed
     */
    public function __get($key) {
        $result = false;

        if($this->modelHasKey($key)) {
            $result = $this->data[$key];
        }

        return $result;
    }

    /**
     * Füge Datum dem Model hinzu, insofern es existiert
     *
     * @param string $key Schlüssel
     * @param mixed $value Wert
     */
    public function __set($key, $value) {
        if($this->modelHasKey($key)) {
            $this->data[$key] = $value;
        }
    }

    /**
     * Speichere Model
     */
    public function save() {
        $this->dbh->query($this->buildSaveQuery());
    }

    /**
     * Lösche Model
     */
    public function delete() {
       $this->dbh->query($this->buildDeleteQuery());
    }

    /**
     * Konstruiere den SQL Query zum Speichern
     */
    private function buildSaveQuery() {
        $sql = null;

        if(isset($this->data[$this->primaryKey])) {
            $sql = "UPDATE {$this->table} SET {$this->getDataSet()} WHERE {$this->primaryKey}={$this->data[$this->primaryKey]};";
        }
        else {
            $sql = "INSERT INTO {$this->table} ({$this->getDataKeys()}) VALUES ({$this->getDataValues()});";
        }

        return $sql;
    }

    /**
     * Konstruiere den SQL Query zum Löschen
     */
    private function buildDeleteQuery() {
        $sql = null;

        if(isset($this->data[$this->primaryKey])) {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey}={$this->data[$this->primaryKey]}";
        }

        return $sql;
    }

    /**
     * Lade Model Daten
     */
    private function load($data) {
        if(!is_null($data)) {
            foreach($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    /**
     * Überprüft ob das Model den Schlüssel enthält
     *
     * @param string $key Zu überprüfender Schlüssel
     *
     * @return bool Enthält true wenn der Schlüssel existiert und false wenn nicht
     */
    private function modelHasKey($key) {
        $hasKey = false;

        if(in_array($key, $this->fillable) || $key === $this->primaryKey || $key === $this->createdAt || $key === $this->updatedAt) {
            $hasKey = true;
        }

        return $hasKey;
    }

    private function modelHasFillable($key) {
        return in_array($key, $this->fillable);
    }

    /**
     * Gibt den Standard Tabellennamen zurückgeben
     *
     * @return string
     */
    private function getDefaultTableName() {
        return strtolower(array_pop(explode("\\", get_class($this)))) . "s";
    }

    /**
     * Gibt DataSet für einen SQL Query wieder
     */
    private function getDataSet() {
        $set = [];

        foreach($this->data as $key => $value) {
            if($this->modelHasFillable($key)) {
                $set[] = "$key={$this->quoteIfString($value)}";
            }
        }

        return implode(',', $set);
    }

    /**
     * Gibt DataKeys für einen SQL Query wieder
     */
    private function getDataKeys() {
        $set = [];

        foreach($this->data as $key => $value) {
            if($this->modelHasFillable($key)) {
                $set[] = $key;
            }
        }

        return implode(',', $set);
    }

    /**
     * Gibt DataValues für einen SQL Query wieder
     */
    private function getDataValues() {
        $set = [];

        foreach($this->data as $key => $value) {
            if($this->modelHasFillable($key)) {
                $set[] = $this->quoteIfString($value);
            }
        }

        return implode(',', $set);
    }

    /**
     * Quote Parameter if it's a String
     *
     * @param $value
     * @return string
     */
    private function quoteIfString($value) {
        return is_string($value) ? "'".$value."'" : $value;
    }
}

?>
