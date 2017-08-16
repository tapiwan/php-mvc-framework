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
	 * Primärschlüssel Spaltenname
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';

	/**
	 * Spaltennamen der dem Model zugehörigen Tabelle
	 *
	 * @var array
	 */
	protected $fillable = [];

	/**
	 * Zeitstempel Spaltennamen
	 *
	 * @var string
	 */
	protected $createdAt = 'created_at';
	protected $updatedAt = 'updated_at';

	/**
	 * Spalten welche nicht als JSON ausgegeben werden sollen
	 *
	 * @var array
	 */
	protected $hidden = [];

	/**
	 * Database Handler der Models
	 *
	 * @var object
	 */
	private $databaseHandler;

	/**
	 * Model constructor.
	 */
	public function __construct($data = null) {
		$this->table = $this->getDefaultTableName();
		$this->databaseHandler = Container::get('database-handler');

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

		if ($this->modelHasKey($key)) {
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
		if ($this->modelHasKey($key)) {
			$this->data[$key] = $value;
		}
	}

	/**
	 * Magische Isset Methode für Datum des Models
	 *
	 * @param $key
	 * @return bool
	 */
	public function __isset($key) {
		return isset($this->data[$key]);
	}

	/**
	 * Magische Unset Methode für Datum des Models
	 *
	 * @param $key
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
		$return   = [];

		$query->selectFrom('*', $static->table);

		foreach ($criteria as $criterion) {
			$query->addCriteria($criterion[0], $criterion[1], $criterion[2], $criterion[3]);
		}

		$result = Container::get('database-handler')->query($query, get_class($static));

		if ($result->success) {
			if (count($result->data) === 1) {
				//Einzelnes Ergebnis gefunden
				$return = $static->fill($result->data[0]);
			} else if (count($result->data) > 1) {
				//Mehrere Ergebnisse gefunden
				$return = [];

				foreach ($result->data as $model) {
					$return[] = (new static())->fill($model);
				}
			} else {
				//Keine Ergebnisse gefunden
				$return = false;
			}
		} else {
			//Keine Ergebnisse gefunden
			$return = false;
		}

		return $return;
	}

	/**
	 * Speichere Model
	 */
	public function save() {
		$result = $this->databaseHandler->query($this->buildSaveQuery());

		//Bei erstmaligem Speichern, lies den erzeugten Primärschlüssel aus
		if ($result->insertId != 0) {
			$this->__set($this->primaryKey, $result->insertId);
		}
	}

	/**
	 * Lösche Model
	 */
	public function delete() {
		$this->databaseHandler->query($this->buildDeleteQuery());
	}

	/**
	 * Konstruiere den SQL Query zum Speichern
	 */
	private function buildSaveQuery() {
		$query = new QueryObject();

		if (isset($this->data[$this->primaryKey])) {
			$this->__set($this->updatedAt, $this->getTimestamp());

			$query->update($this->getTable(), $this->getData())
			      ->where($this->primaryKey, '=', $this->getPrimaryKeyValue());
		} else {
			$query->insertInto($this->getTable(), $this->getData());
		}

		return $query;
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

		return $query;
	}

	/**
	 * Lade Model Daten
	 */
	private function fill($model) {
		//Wenn das Model neu ist setze Zeitstempel
		$this->__set($this->createdAt, $this->getTimestamp());

		//Wenn das Model geladen wird befülle Daten des Models
		if (!is_null($model) && (is_array($model) || is_object($model))) {
			foreach ($model as $key => $value) {
				$val = empty($value) ? null : $value;

				$this->__set($key, $val);
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

		if (in_array($key, $this->fillable) || $key === $this->primaryKey || $key === $this->createdAt || $key === $this->updatedAt) {
			$hasKey = true;
		}

		return $hasKey;
	}

	/**
	 * Erzeuge einen Tabellennamen anhand des Klassennamens nach Konventionen
	 *
	 * Somit wird z.B. "bitbetrieb\CMS\Model\User" umgewandelt zu "users"
	 *
	 * @param $class
	 * @return string
	 */
	private function getTableNameFromClassName($class) {
		return strtolower(array_pop(explode("\\", $class)))."s";
	}

	/**
	 * Gibt den Standard Tabellennamen zurückgeben
	 *
	 * @return string
	 */
	private function getDefaultTableName() {
		return $this->getTableNameFromClassName(get_class($this));
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
