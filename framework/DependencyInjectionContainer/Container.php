<?php

namespace bitbetrieb\CMS\DependencyInjectionContainer;

class Container implements IContainer {
    /**
     * Map mit allen Werten und Services
     *
     * @var array $map Array welches die Komponenten und deren Abhängigkeiten enthält
     */
    private static $map = [];

    /**
     * JSON Datei mit allen Werten und Services
     *
     * @param string $json
     */
    public static function initializeViaJSON($json) {
        # Read file
        $mapJSON = json_decode($json);

        # Add namespaces and base directories from JSON to autoloader
        foreach($mapJSON as $item) {
            if($item->type === 'value') {
                self::addValue($item->id, $item->value);
            }
            else if($item->type === 'class') {
                self::addClass($item->id, $item->class, $item->dependencies);
            }
            else if($item->type === 'singleton') {
                self::addSingleton($item->id, $item->class, $item->dependencies);
            }
        }
    }

    /**
     * Wert zur Map hinzufügen
     *
     * @param $id
     * @param $value
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
     * @param $id
     * @param $value
     * @param null $dependencies
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
     * @param $id
     * @param $value
     * @param null $dependencies
     */
    public static function addClass($id, $value, $dependencies = null) {
        self::$map[$id] = (object)[
            "value" => $value,
            "type" => "class",
            "dependencies" => $dependencies
        ];
    }

    /**
     * Wert, Singleton oder Class nach $id zurückgeben
     *
     * @param $id
     * @return object
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
}

?>
