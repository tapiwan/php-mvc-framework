<?php

namespace bitbetrieb\MVC\DependencyInjectionContainer;

/**
 * Class Container
 * @package bitbetrieb\MVC\DependencyInjectionContainer
 */
class Container implements IContainer {
    /**
     * Map mit allen Werten und Services
     *
     * @var array $map Array welches die Komponenten und deren Abhängigkeiten enthält
     */
    private static $map = [];

    /**
     * Wert zur Map hinzufügen
     *
     * @param string $id Der Identifier als ein String, frei wählbar
     * @param mixed $value Der Wert, kann von jeglichem Datentyp sein
     */
    public static function addValue($id, $value) {
        self::$map[$id] = (object)[
            "value" => $value,
            "type" => "value"
        ];
    }

    /**
     * Singleton zur Map hinzufügen
     *
     * @param string $id Der Identifier als ein String, frei wählbar
     * @param string $value Der Klassenname des Singletons mit Namespace
     * @param array|null $dependencies Array mit Identifiern anderer Dependencies. Kann null oder ein leeres Array sein.
     */
    public static function addSingleton($id, $value, $dependencies = null) {
        self::$map[$id] = (object)[
            "value" => $value,
            "type" => "singleton",
            "dependencies" => $dependencies,
            "instance" => null
        ];
    }

    /**
     * Class zur Map hinzufügen
     *
     * @param string $id Der Identifier als ein String, frei wählbar
     * @param string $value Der Klassenname mit Namespace
     * @param array|null $dependencies Array mit Identifiern anderer Dependencies. Kann null oder ein leeres Array sein.
     */
    public static function addClass($id, $value, $dependencies = null) {
        self::$map[$id] = (object)[
            "value" => $value,
            "type" => "class",
            "dependencies" => $dependencies
        ];
    }

    /**
     * Überprüft ob die Dependency existiert
     *
     * @param $id
     * @return bool
     */
    public static function has($id) {
        return isset(self::$map[$id]);
    }

    /**
     * Value, Singleton oder Class anhand von Identifier zurückgeben.
     * In diesem Zuge auch die Dependencies rekursiv auflösen.
     *
     * @param string $id Der Identifier
     * @return object Enthält entweder den Wert (beim Typ 'values)', eine neue Instanz der Klasse (beim Typ 'class') oder die Singleton Instanz (beim Typ 'singleton') der Dependency
     * @throws \Exception
     */
    public static function get($id) {
        $item = self::$map[$id];

        if(!isset($item)) {
            throw new \Exception("Dependency Injection: item with id '" . $id . "' is not mapped");
        }

        if($item->type === 'value') {
            return $item->value;
        }
        else {
            //Überprüfen ob die Klasse existiert
            if(!class_exists($item->value)) {
                throw new \Exception("Dependency Injection: missing class '" . $item->value . "'");
            };

            //Wenn es ein Singleton ist und eine Instanz existiert gib diese zurück
            if($item->type === 'singleton' && $item->instance !== null) {
                return $item->instance;
            }

            //Wenn es keine Abhängigkeiten gibt erzeuge eine Instanz
            if($item->dependencies === null || count($item->dependencies) === 0) {
                return new $item->value;
            }
            //Wenn es Abhängigkeiten gibt erzeuge diese rekursiv und anschließend eine Instanz der angefragten Klasse
            else {
                if(!is_array($item->dependencies)) {
                    $item->dependencies = array($item->dependencies);
                }

                //Löse Abhängigkeiten auf
                $arguments = [];
                foreach($item->dependencies as $dependencyId) {
                    array_push($arguments, self::get($dependencyId));
                }

                //Erzeuge Instanz mit Abhängigkeiten
                $reflector = new \ReflectionClass($item->value);
                $instance = $reflector->newInstanceArgs($arguments);

                //Wenn es ein Singleton ist speichere Instanz
                if($item->type === 'singleton') {
                    $item->instance = $instance;
                }

                return $instance;
            }
        }
    }

	/**
    * Lädt eine Konfigurationsdatei des Containers
    *
	 * @param $file
	 */
    public static function load($file) {
        require_once($file);
    }
}

?>
