<?php

namespace bitbetrieb\CMS\ServiceContainer;

class Container implements IContainer {
   private $services = [];

   public function initializeServicesFromJSON($pathToFile) {
      # Read file
      $servicesJSON = json_decode(file_get_contents(realpath($pathToFile)));

      # Add namespaces and base directories from JSON to autoloader
      foreach($servicesJSON as $service) {
         $this->addService($service->id, $service->class, $service->singleton, $service->dependencies);
      }
   }

   public function addValue($id, $value) {
      
   }

   public function addService($id, $class, $singleton = true, $dependencies = null) {
      $this->services[$id] = (object) [
         "class" => $class,
         "dependencies" => $dependencies,
         "singleton" => $singleton,
         "instance" => null
      ];
   }

   public function get($id) {
      //Service auslesen
      $service = $this->services[$id];

      //Überprüfen ob der Service registriert ist
      if(!isset($service)) {
         throw new \Exception("Dependency Injection: missing service '".$id."'");
      }

      //Überprüfen of die Klasse des Services existiert
      if(!class_exists($service->class)) {
         throw new \Exception("Dependency Injection: missing class '".$service->class."'");
      }

      //Wenn der Service schon eine Instanz hat und ein Singleton ist gebe ihn zurück
      if($service->singleton && $service->instance !== null) {
         return $service->instance;
      }

      //Wenn der Service keine Abhängigkeiten hat, erzeuge eine Instanz
      if($service->dependencies === null || count($service->dependencies) === 0) {
         $service->instance = new $service->class;
      }
      //Wenn der Service Abhängigkeiten hat dann erzeugte rekursiv Instanzen dieser Abhängigkeiten
      else {
         if(!is_array($service->dependencies)) {
            $service->dependencies = array($service->dependencies);
         }

         //Löse Abhängigkeiten auf
         $arguments = [];
         foreach($service->dependencies as $dependencyId) {
            array_push($arguments, $this->get($dependencyId));
         }

         //Erzeuge Service Instanz mit Abhängigkeiten
         $reflector = new \ReflectionClass($service->class);
         $service->instance = $reflector->newInstanceArgs($arguments);
      }

      return $service->instance;
   }
}

?>
