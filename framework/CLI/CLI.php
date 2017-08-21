<?php

namespace bitbetrieb\CMS\CLI;

/**
 * Class CLI
 * @package bitbetrieb\CMS\CLI
 */
class CLI {
    private $scriptName;
    private $baseDir;
    private $cmd;
    private $_allowedCmds = [
        'make:controller' => "createController",
        'make:model' => "createModel",
        'make:table' => "createTable",
        'merge:tables' => "mergeTables"
    ];

    /**
     * CLI constructor.
     *
     * @param $argv
     * @param string $baseDir
     */
    public function __construct($argv, $baseDir = __DIR__) {
        $this->baseDir = $baseDir;
        $this->readCmd($argv);
    }

    /**
     * Starte das CLI
     */
    public function start() {
        $this->runCmd();
    }

    /**
     * Lies as eingegebene Kommando aus
     *
     * @param $argv
     */
    private function readCmd($argv) {
        //Entferne ersten Parameter, da es sich um den Skriptnnamen handelt
        $this->scriptName = array_shift($argv);
        $this->cmd = array_shift($argv);
    }

    /**
     * Führe die dem Kommando zugehörige Funktion aus
     */
    private function runCmd() {
        if(isset($this->_allowedCmds[$this->cmd])) {
            $method = $this->_allowedCmds[$this->cmd];

            if(method_exists($this, $method)) {
                $this->$method();
            } else {
                echo "No matching function found for known command.";
            }
        } else {
            echo "Unknown command.";
        }
    }

    /**
     * Erstelle einen Controller
     */
    private function createController() {
        echo 'Please enter the name of the controller: ';

        $controllerName = $this->readInput();

        $controllerTemplate = $this->readTemplate("/templates/controller.php.tpl");
        $controllerTemplate = $this->replaceTag("{ControllerName}", $controllerName, $controllerTemplate);

        $this->createFile("app/controller", $controllerName, "php", $controllerTemplate);
    }

    /**
     * Erstelle ein Model
     */
    private function createModel() {
        echo 'Please enter the name of the model: ';

        $modelName = $this->readInput();

        $modelTemplate = $this->readTemplate("/templates/model.php.tpl");
        $modelTemplate = $this->replaceTag("{ModelName}", $modelName, $modelTemplate);

        $this->createFile("app/models", $modelName, "php", $modelTemplate);
    }

    /**
     * Erstelle ein Model
     */
    private function createTable() {
        echo 'Please enter the table name: ';
        $tableName = $this->readInput();
        $fileName = "create_".$tableName."_table";

        $tableTemplate = $this->readTemplate("/templates/table.sql.tpl");
        $tableTemplate = $this->replaceTag("{FileName}", $fileName, $tableTemplate);
        $tableTemplate = $this->replaceTag("{TableName}", $tableName, $tableTemplate);

        $this->createFile("app/tables", $fileName, "sql", $tableTemplate);
    }

    /**
     * Erstelle gesamte SQL-Datei
     */
    private function mergeTables() {
        echo 'Do you want to create a single SQL file out of the individual table files? Answer Y/N: ';
        $answer = $this->readInput();
        $answer = strtolower($answer)[0];

        if($answer === 'y') {
            $files = scandir($this->baseDir.DIRECTORY_SEPARATOR."app/tables", SCANDIR_SORT_ASCENDING);
            $sql = "";

            foreach($files as $file) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);

                if($ext === 'sql') {
                    $sql .= file_get_contents($this->baseDir.DIRECTORY_SEPARATOR."app/tables".DIRECTORY_SEPARATOR.$file)."\n";
                }
            }

            $this->createFile("app/tables", "schema", "sql", $sql, false);
        }
        else {
            echo 'Cancelled';
        }
    }

    /**
     * Lies Nutzereingabe
     *
     * @return string
     */
    private function readInput() {
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        return trim($line);
    }

    /**
     * Erstelle eine Datei in einem Ordner mit Namen und Inhalt
     *
     * @param $directory
     * @param $name
     * @param $content
     */
    private function createFile($directory, $name, $extension, $content, $dontOverwrite = true) {
        $dir = str_replace("/", DIRECTORY_SEPARATOR, $directory);
        $dir = str_replace("\\", DIRECTORY_SEPARATOR, $dir);;

        $path = $this->baseDir . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $name.".".$extension;

        if(file_exists($path) && $dontOverwrite) {
            echo "Couldn't create '$name' because file already exists";
        } else {
            $handle = fopen($path, "w+");
            fwrite($handle, $content);
            fclose($handle);

            echo "Successfully created '$name.$extension'";
        }
    }

    /**
     * Lies ein Template ein
     *
     * @param $file
     * @return bool|string
     */
    private function readTemplate($file) {
        return file_get_contents(__DIR__ . $file);
    }

    /**
     * Ersetze einen Tag in einem Template
     *
     * @param $tag
     * @param $replacement
     * @param $template
     * @return mixed
     */
    private function replaceTag($tag, $replacement, $template) {
        return str_replace($tag, $replacement, $template);
    }
}

?>