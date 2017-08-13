<?php

namespace bitbetrieb\CMS\Model;

use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;
use bitbetrieb\CMS\DatabaseHandler\QueryObject as QueryObject;

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

        $this->fill($data);
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
     * Suche Model
     */
    public static function find() {
        $query = new QueryObject();
        $static = new static();
        $criteria = func_get_args();
        $return = null;

        $query->selectFrom('*', $static->table);

        foreach($criteria as $criterion) {
            if($criterion[0] === 'where') {
                $query->where($criterion[1], $criterion[2], $criterion[3]);
            }
            else if($criterion[0] === 'and') {
                $query->_and($criterion[1], $criterion[2], $criterion[3]);
            }
            else if($criterion[0] === 'or') {
                $query->_or($criterion[1], $criterion[2], $criterion[3]);
            }
        }

        $result = Container::get('database-handler')->query($query);

        if(count($result) === 1) {
            $return = $static->fill($result);
        }
        else if(count($result) > 1) {
            $return = [];

            foreach($result as $model) {
                $return[] = (new static())->fill($model);
            }
        }
        else {
            $return = false;
        }

        return $return;
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
        $query = new QueryObject();

        if(isset($this->data[$this->primaryKey])) {
            $query->update($this->table, $this->getData())->where($this->primaryKey, '=', $this->getPrimaryKeyValue());
        }
        else {
            $query->insertInto($this->table, $this->getData());
        }

        return $query;
    }

    /**
     * Konstruiere den SQL Query zum Löschen
     */
    private function buildDeleteQuery() {
        $query = new QueryObject();

        if(isset($this->data[$this->primaryKey])) {
            $query->deleteFrom($this->table)->where($this->primaryKey, '=', $this->getPrimaryKeyValue());
        }

        return $query;
    }

    /**
     * Lade Model Daten
     */
    private function fill($model) {
        if(!is_null($model)) {
            foreach($model as $data) {
                foreach($data as $key => $value) {
                    $this->__set($key, $value);
                }
            }
        }

        return $this;
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

        if(in_array($key, $this->fillable) || $key === $this->primaryKey) {
            $hasKey = true;
        }

        return $hasKey;
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
     * Gibt Data zurück
     *
     * @param bool $skipPrimaryKey Entscheidet ob der Primärschlüssel übersprungen werden soll
     *
     * @return array Assoziatives Array mit den Daten des Models
     */
    private function getData($skipPrimaryKey = true) {
        $data = [];

        foreach($this->data as $key => $value) {
            if($skipPrimaryKey && $key == $this->primaryKey) {
                    continue;
            }

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Gibt den Wert des Primary Keys zurück
     *
     * @return mixed
     */
    private function getPrimaryKeyValue() {
        return $this->data[$this->primaryKey];
    }
}

?>
