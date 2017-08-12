<?php

namespace bitbetrieb\CMS\JsonHandler;

/**
 * Class JsonHandler
 * @package bitbetrieb\CMS\JsonHandler
 */
class JsonHandler {
    /**
     * Decode the JSON file to an object
     *
     * @param $file
     * @return object
     */
    public static function decodeAsObject($file) {
        return self::decode($file, false);
    }

    /**
     * Decode the JSON file to an array
     *
     * @param $file
     * @return array
     */
    public static function decodeAsArray($file) {
        return self::decode($file, true);
    }

    /**
     * Decode the JSON file to array or object, depending on second parameter
     *
     * @param $file
     * @param bool $asArray If true converts JSON to array. If false converts JSON to object
     *
     * @return object|array
     *
     * @throws \Exception
     */
    private static function decode($file, $asArray = true) {
        if(!file_exists($file)) {
            throw new \Exception("JSON file '$file' missing.");
        }
        return json_decode(file_get_contents($file), $asArray);
    }

    /**
     * Encode object or array to JSON string
     *
     * @param array|object $arrayOrObject Array or object to be converted to JSON
     *
     * @return string The JSON String
     */
    public static function encode($arrayOrObject) {
        return json_encode($arrayOrObject);
    }
}

?>