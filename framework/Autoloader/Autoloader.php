<?php

namespace bitbetrieb\MVC\Autoloader;

/**
 * Class Autoloader
 * @package bitbetrieb\MVC\Autoloader
 */
class Autoloader {
    /**
     * Ein assoziatives Array, bei dem der Schlüssel der Namespace Prefix und der Wert
     * ein Array mit den dazugehörigen Quellordnern für die Klassen des Namespaces ist.
     *
     * @var array
     */
    private $prefixes = [];

    /**
     * Den Autoloader im SPL Autoloader Stack registrieren
     *
     * @return void
     */
    public function register() {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * Fügt einen Quellordner zu einem Namespace Prefix hinzu
     *
     * @param string $prefix Der Namespace Prefix
     * @param string $base_dir Ein Quellordner für die Klassen innerhalb des Namespaces
     * @param bool $prepend Wenn true, fügt den Quellordner vorne an die Liste der Quellordner an.
     * Somit wird er früher durchsucht. Wenn false wird der Quellordner hinten angefügt
     *
     * @return void
     */
    public function addNamespace($prefix, $base_dir, $prepend = false) {
        //Namespace Prefix normalisieren
        $prefix = trim($prefix, '\\') . '\\';

        //Quellordner normalisieren mit einem abschließenden Slash
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        //Namespace Array initialisieren
        if (isset($this->prefixes[$prefix]) === false) {
            $this->prefixes[$prefix] = [];
        }

        //Füge Quellordner vorne oder hinten an den Liste der Quellordner an
        if($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            array_push($this->prefixes[$prefix], $base_dir);
        }
    }

    /**
     * Lies JSON Zeichenkette ein und füge die enthaltenen Namespaces mit zugehörigen Quellordner dem Autoloader hinzu
     *
     * @param string $json JSON Zeichenkette
     *
     * @return void
     */
    public function initializeViaJSON($json) {
        //JSON Zeichenkette decodieren
        $autoloadJSON = json_decode($json, true);

        //Namespace Prefix mit Quellordnern hinzufügen
        foreach($autoloadJSON as $namespacePrefix => $baseDirectories) {
            foreach($baseDirectories as $baseDirectory) {
                $this->addNamespace($namespacePrefix, realpath('../' . $baseDirectory));
            }
        }

        $this->register();
    }

    /**
     * Lädt die Datei einer Klasse anhand ihres Klassennamens mit Namespace
     *
     * @param string $class Der Klassenname mit Namespace
     *
     * @return mixed Enthält bei Erfolg die gefundene Datei, bei Fehler false
     */
    public function loadClass($class) {
        //Der Namespace Prefix
        $prefix = $class;

        //Rückwärts durch den Prefix gehen um eine Datei zu finden
        while(false !== $pos = strrpos($prefix, '\\')) {

            //Den abschließenden Separator des Prefix erhalten
            $prefix = substr($class, 0, $pos + 1);

            //Der Rest ist der relative Klassenname
            $relative_class = substr($class, $pos + 1);

            //Versuche Datei für den Namespace Prefix und den relativen Klassenname zu laden
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if($mapped_file) {
                return $mapped_file;
            }

            //Entferne abschließenden Separator des Prefix für nächste Iteration
            $prefix = rtrim($prefix, '\\');
        }

        //Es konnte keine Datei gefunden werden
        return false;
    }

    /**
     * Die gefundene Datei einer Klasse anhand von Namespace Prefix und relativem Klassenname laden
     *
     * @param string $prefix Der Namespace Prefix
     * @param string $relative_class Der relative Klassenname
     *
     * @return mixed boolean Enthält false wenn keine Datei geladen werden konnte.
     * Bei Erfolg ist der Name der geladenen Datei enthalten
     */
    protected function loadMappedFile($prefix, $relative_class) {
        //Überprüfen ob der Namespace Prefix registriert ist
        if(isset($this->prefixes[$prefix]) === false) {
            return false;
        }

        //Quellordner des Namespace Prefix durchschauen
        foreach($this->prefixes[$prefix] as $base_dir) {

            //Namespace Prefix mit Quellordner-Pfad ersetzen
            //Namespace Separatoren mit Pfad-Separatoren ersetzen
            //.php an den relativen Klassennamen anhängen
            $file = $base_dir
                . str_replace('\\', '/', $relative_class)
                . '.php';

            //Wenn die Klassendatei existiert, lade sie...
            if($this->requireFile($file)) {
                return $file;
            }
        }

        //Es konnte keine Klassendatei gefunden werden
        return false;
    }

    /**
     * Wenn eine Datei existiert, lade sie vom Dateisystem
     *
     * @param string $file Die zu ladende Datei
     *
     * @return bool Enthält true wenn die Datei existiert und false wenn nicht
     */
    protected function requireFile($file) {
        if(file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}

?>
