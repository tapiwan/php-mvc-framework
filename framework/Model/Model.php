<?php

namespace bitbetrieb\CMS\Model;

use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;

/**
 * Class Model
 * @package bitbetrieb\CMS\Model
 */
abstract class Model {
    /**
     * Klassenname des Models mit Namespace
     *
     * @var string
     */
    protected $class;

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
     * Spaltenname mit Datentypen der dem Model zugehörigen Tabelle
     *
     * @var array
     */
    protected $fillable = [
        'id' => 'int'
    ];

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
     * Array um die Suchkriterien der get Methode zu speichern
     *
     * @var array
     */
    public static $findCriteria = [];

    /**
     * Model constructor.
     */
    public function __construct($data = null) {
        //Klassenname auslesen
        $this->class = get_class($this);

        //Standard-Tabellenname ist Klassenname ohne Namespace, kleingeschrieben mit angehängtem "s"
        //Beispiel: bitbetrieb/CMS/Model/User -> users
        $this->table = strtolower(array_pop(explode("\\", $this->class))) . "s";

        //Hole Database Handler aus Container
        $this->dbh = Container::get('database-handler');

        //Falls Daten übergeben wurden lade das Model
        if(!is_null($data)) {
            $this->load($data);
        }
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
     * Gibt den Tabellenname zurück
     *
     * @return string Tabellenname
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * Gibt den Klassennamen zurück
     */
    public static function getClassStatic() {
        return get_class(new static());
    }

    /**
     * Gibt den Tabellennamen der Klasse zurück
     */
    public static function getTableStatic() {
        $static = new static();
        return $static->getTable();
    }

    /**
     * Suchkriterium dem Model hinzufügen
     *
     * @param string $key Schlüssel des Kriteriums
     * @param string $operator Vergleichsoperator
     * @param mixed $value Wert des Kriteriums
     */
    public static function criteria($cmd, $key, $operator, $value) {
        self::$findCriteria[] = (object)[
            'cmd' => $cmd,
            'key' => $key,
            'operator' => $operator,
            'value' => $value
        ];
    }

    /**
     * Finde Einträge des Models anhand von Kriterien
     *
     * @param array $criteria
     */
    public static function find() {
        $result = Container::get('database-handler')->query(self::buildFindQuery());
        $models = [];

        foreach($result as $data) {
            $reflector = new \ReflectionClass(self::getClassStatic());
            $models[] = $reflector->newInstance($data);
        }

        if(count($models) === 1) {
            return $models[0];
        }

        return $models;
    }

    /**
     * Speichere Model
     */
    public function save() {
        $this->dbh->query($this->buildSaveQuery(), true);

        return $this;
    }

    /**
     * Lösche Model
     */
    public function delete() {
        $this->dbh->query($this->buildDeleteQuery(), true);

        return $this;
    }

    /**
     * Lade Model Daten
     */
    private function load($data) {
        foreach($data as $key => $value) {
            if($this->modelHasKey($key)) {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * Konstruiere SQL Query zum Auffinden des Models anhand von Bedingungen
     *
     * @return string SQL Query
     */
    private static function buildFindQuery() {
        return "SELECT * FROM " . self::getTableStatic() . " " . self::getFindCriteria() . ";";
    }

    /**
     * Konstruiere SQL Query zum Speichern des Models
     *
     * @return string
     */
    private function buildSaveQuery() {
        return "INSERT INTO {$this->getTable()} ({$this->getDataKeys()}) VALUES ({$this->getDataValues()}) ON DUPLICATE KEY UPDATE {$this->getDataKeysAndValues()};";
    }

    /**
     * Konstruiere SQL Query zum Löschen des Models
     *
     * @return string
     */
    private function buildDeleteQuery() {
        return "DELETE FROM {$this->getTable()} WHERE id={$this->id}";
    }

    public static function getFindCriteria() {
        $coll = [];

        if(count(self::$findCriteria) > 0) {
            foreach(self::$findCriteria as $criterion) {
                if(is_string($criterion->value)) {
                    $coll[] = $criterion->cmd . " " . $criterion->key . $criterion->operator . '"' . $criterion->value . '"';
                } else {
                    $coll[] = $criterion->cmd . " " . $criterion->key . $criterion->operator . $criterion->value;
                }
            }
        }

        return implode(" ", $coll);
    }

    /**
     * Gibt Schlüssel aller Model Daten zurück so wie sie für einen SQL String benötigt werden
     *
     * @return string
     */
    private function getDataKeys() {
        $keys = [];

        foreach($this->data as $key => $value) {
            if($this->modelHasKey($key)) {
                $keys[] = $key;
            }
        }

        return implode(",", $keys);
    }

    /**
     * Gibt Werte aller Model Daten zurück so wie sie für einen SQL String benötigt werden
     *
     * @return string
     */
    private function getDataValues() {
        $values = [];

        foreach($this->data as $key => $value) {
            if($this->modelHasKey($key)) {
                if(is_string($value)) {
                    $values[] = '"' . $value . '"';
                } else {
                    $values[] = $value;
                }
            }
        }

        return implode(",", $values);
    }

    /**
     * Gibt Werte und Schlüssel aller Model Daten zurück so wie sie für einen SQL String benötigt werden
     *
     * @return string
     */
    private function getDataKeysAndValues() {
        $coll = [];

        foreach($this->data as $key => $value) {
            if($this->modelHasKey($key)) {
                if(is_string($value)) {
                    $coll[] = $key . '="' . $value . '"';
                } else {
                    $coll[] = $key . '=' . $value;
                }
            }
        }

        return implode(",", $coll);
    }

    /**
     * Überprüft ob das Model den Schlüssel enthält
     *
     * @param string $key Zu überprüfender Schlüssel
     *
     * @return bool Enthält true wenn der Schlüssel existiert und false wenn nicht
     */
    private function modelHasKey($key) {
        return isset($this->fillable[$key]);
    }
}

?>
