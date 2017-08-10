<?php

namespace bitbetrieb\CMS\PathFinder;

class PathFinder {
    /**
     * Get the document root of the application
     *
     * @return string
     */
    public function document_root() {
        return $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR;
    }

    /**
     * Get the base directory of the application (one level above document root)
     *
     * @return string
     */
    public function base_dir() {
        return dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR;
    }

    /**
     * Get a directory from inside the base directory, optionally extend the path with the parameter
     *
     * @param string $path
     * @return string
     */
    public function dir($path = "") {
        $path = str_replace("/", DIRECTORY_SEPARATOR, $path);
        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);

        return $this->base_dir().$path;
    }
}

?>