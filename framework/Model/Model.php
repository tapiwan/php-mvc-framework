<?php

namespace bitbetrieb\MVC\Model;

use bitbetrieb\MVC\DatabaseHandler\IDatabaseHandler;
use bitbetrieb\MVC\DatabaseHandler\QueryObject as QueryObject;
use bitbetrieb\MVC\DependencyInjectionContainer\Container as Container;

/**
 * Class Model
 * @package bitbetrieb\MVC\Model
 */
abstract class Model implements IModel {
	/**
	 * Zum Model zugehöriger Tabellenname
	 *
	 * Wird automatisch erzeugt, kann aber überschrieben werden
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * Assoziatives Array mit Daten des Models
	 *
	 * Kann nur folgende Schlüssel enthalten:
	 * $primaryKey, Schlüssel aus $fillable, $createdAt und $updatedAt
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Primärschlüssel des Models
	 *
	 * Ist ebenfalls der Spaltenname in der Datenbank-Tabelle
	 *
	 * @var string
	 */
	protected $primaryKey;

	/**
	 * Fremdschlüssel des Models
	 *
	 * Ist ebenfalls Spaltenname in der Datenbank-Tabelle
	 *
	 * @var string
	 */
	protected $foreignKey;

	/**
	 * Datenschlüssel des Models
	 *
	 * Jeder Eintrag stellt ein Datum des Models und die zugehörige
	 * Spalte in der Datenbank-Tabelle dar
	 *
	 * @var array
	 */
	protected $fillable = [];

	/**
	 * Zeitstempel des Models
	 *
	 * Sind ebenfalls Spaltennamen in der Datenbank-Tabelle
	 *
	 * @var string
	 */
	protected $createdAt;
	protected $updatedAt;

	/**
	 * Daten welche nicht als JSON ausgegeben werden
	 *
	 * Versteckt die angegebenen Schlüssel aus $data
	 *
	 * @var array
	 */
	protected $hidden = [];

	/**
	 * Database Handler des Models
	 *
	 * @var IDatabaseHandler
	 */
	protected $databaseHandler;

	/**
	 * Model constructor.
	 */
	public function __construct() {
		$this->table           = $this->getDefaultTableName();
		$this->primaryKey      = 'id';
		$this->foreignKey      = $this->getDefaultForeignKey();
		$this->createdAt       = 'created_at';
		$this->updatedAt       = 'updated_at';
		$this->databaseHandler = Container::get('database-handler');

		//Zeitstempel setzen
		$this->{$this->createdAt} = $this->getTimestamp();
	}

	/**
	 * Gib Datum des Models anhand von einem Schlüssel aus, insofern er existiert
	 *
	 * @param string $key Schlüssel des Datums
	 *
	 * @return mixed
	 */
	public function __get($key) {
		$result = false;

		if ($this->modelHasFillable($key)) {
			$result = $this->data[$key];
		}

		return $result;
	}

	/**
	 * Füge Datum dem Model hinzu, insofern es existiert
	 *
	 * @param string $key Schlüssel des Datums
	 * @param mixed $value Wert des Datums
	 */
	public function __set($key, $value) {
		if ($this->modelHasFillable($key)) {
			$this->data[$key] = $value;
		}
	}

	/**
	 * Überprüfe ob ein Datum des Models existiert
	 */
	public function __isset($key) {
		return isset($this->data[$key]);
	}

	/**
	 * Lösche Datum des Models
	 */
	public function __unset($key) {
		unset($this->data[$key]);
	}

	/**
	 * Suche Models
	 *
	 * @return array|bool Wurde ein Model gefunden ist das Model enthalten. Wurden mehrere Models gefunden ist ein Array
	 * von Models enthalten
	 */
	public static function find() {
		$query    = new QueryObject();
		$static   = new static();
		$criteria = func_get_args();
		$return   = false;

		$query->select('*')
		      ->from($static->table)
		      ->addCriteria($criteria);

		$result = Container::get('database-handler')
		                   ->query($query->assemble());

		if ($result->getSuccess()) {
			if (count($result->getData()) === 1) {
				//Einzelnes Ergebnis gefunden
				$return = $static->fill($result->getData()[0]);
			} else if (count($result->getData()) > 1) {
				//Mehrere Ergebnisse gefunden
				$return = [];

				foreach ($result->getData() as $model) {
					$return[] = (new static())->fill($model);
				}
			}
		}

		return $return;
	}

	/**
	 * Speichere Model
	 */
	public function save() {
		$result = $this->databaseHandler->query($this->buildSaveQuery());

		//Bei erstmaligem Speichern:
		//lies den erzeugten Primärschlüssel aus und setze Datum des Models
		if ($result->getLastInsertId() != 0) {
			$this->{$this->primaryKey} = $result->getLastInsertId();
		}
	}

	/**
	 * Lösche Model
	 */
	public function delete() {
		$result = $this->databaseHandler->query($this->buildDeleteQuery());

		return $result->getSuccess();
	}

	/**
	 * Konstruiere den SQL Query zum Speichern
	 */
	private function buildSaveQuery() {
		$query = new QueryObject();

		if (isset($this->data[$this->primaryKey])) {
			$this->{$this->updatedAt} = $this->getTimestamp();

			$query->update($this->getTable(), $this->getData())
			      ->where($this->primaryKey, '=', $this->getPrimaryKeyValue());
		} else {
			$query->insertInto($this->getTable(), $this->getData());
		}

		return $query->assemble();
	}

	/**
	 * Konstruiere den SQL Query zum Löschen
	 */
	private function buildDeleteQuery() {
		$query = new QueryObject();

		if (isset($this->data[$this->primaryKey])) {
			$query->deleteFrom($this->getTable())
			      ->where($this->primaryKey, '=', $this->getPrimaryKeyValue());
		}

		return $query->assemble();
	}

	/**
	 * Lade Model Daten
	 */
	public function fill($model) {
		//Wenn das Model geladen wird befülle Daten des Models
		if (!is_null($model) && (is_array($model) || is_object($model))) {
			foreach ($model as $key => $value) {
				$val = empty($value) ? null : $value;

				$this->{$key} = $val;
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
	private function modelHasFillable($key) {
		$hasKey = false;

		if (in_array($key, $this->fillable) || $key === $this->primaryKey || $key === $this->createdAt || $key === $this->updatedAt) {
			$hasKey = true;
		}

		return $hasKey;
	}

	/**
	 * Gib den Klassennamen ohne Namespace und kleingeschrieben zurück
	 *
	 * Somit wird z.B. "bitbetrieb\MVC\Model\User" => "user"
	 *
	 * @param $class
	 * @return string
	 */
	private function getClassNameWithoutNS($class) {
		return strtolower(array_pop(explode("\\", $class)));
	}

	/**
	 * Gibt den Standard Tabellennamen zurückgeben
	 *
	 * @return string
	 */
	private function getDefaultTableName() {
		return $this->getClassNameWithoutNS(get_class($this))."s";
	}

	/**
	 * Gibt den Standard Spaltennamen des Fremdschlüssels zurück
	 *
	 * @return string
	 */
	private function getDefaultForeignKey() {
		return $this->getClassNameWithoutNS(get_class($this))."_id";
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

		foreach ($this->data as $key => $value) {
			if ($skipPrimaryKey && $key == $this->primaryKey) {
				continue;
			}

			$data[$key] = $value;
		}

		return $data;
	}

	/**
	 * Gibt einen Zeitstempel im SQL Format zurück
	 *
	 * @return false|string
	 */
	private function getTimestamp() {
		return date("Y-m-d H:i:s");
	}

	/**
	 * Gibt den Wert des Primary Keys zurück
	 *
	 * @return mixed
	 */
	private function getPrimaryKeyValue() {
		return $this->data[$this->primaryKey];
	}

	/**
	 * Gibt den Tabellenname zurück
	 *
	 * @return string
	 */
	public function getTable() {
		return $this->table;
	}
}

?>
