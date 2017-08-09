<?php

namespace bitbetrieb\CMS\DependencyInjectionContainer;

class Container implements IContainer {
    private $map = [];

    public function initializeMapFromJSON($pathToFile) {
        # Read file
        $mapJSON = json_decode(file_get_contents(realpath($pathToFile)));

        # Add namespaces and base directories from JSON to autoloader
        foreach($mapJSON as $item) {
            if($item->type === 'value') {
                $this->addValue($item->id, $item->value);
            }
            else if($item->type === 'class') {
                $this->addClass($item->id, $item->class, $item->dependencies);
            }
            else if($item->type === 'singleton') {
                $this->addSingleton($item->id, $item->class, $item->dependencies);
            }
        }
    }

    public function addValue($id, $value) {
        $this->map[$id] = (object)[
            "value" => $value,
            "type" => "value"
        ];
    }

    public function addSingleton($id, $value, $dependencies = null) {
        $this->map[$id] = (object)[
            "value" => $value,
            "type" => "singleton",
            "dependencies" => $dependencies,
            "instance" => null
        ];
    }

    public function addClass($id, $value, $dependencies = null) {
        $this->map[$id] = (object)[
            "value" => $value,
            "type" => "class",
            "dependencies" => $dependencies
        ];
    }

    public function get($id) {
        $item = $this->map[$id];

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
                    array_push($arguments, $this->get($dependencyId));
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
