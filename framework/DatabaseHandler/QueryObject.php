<?php

namespace bitbetrieb\MVC\DatabaseHandler;

class QueryObject implements IQueryObject {
    private $queryParts;

    public function selectFrom($columns, $tables) {
        $columnsString = $this->columnsToString($columns);
        $tablesString = $this->tablesToString($tables);

        $this->addQueryPart("SELECT $columnsString FROM $tablesString");

        return $this;
    }

    public function insertInto($tables, $data) {
        $tablesString = $this->tablesToString($tables);
        $keysString = $this->dataKeysToString($data);
        $valuesString = $this->dataValuesToString($data);

        $this->addQueryPart("INSERT INTO $tablesString ($keysString) VALUES ($valuesString)");

        return $this;
    }

    public function update($tables, $data) {
        $tablesString = $this->tablesToString($tables);
        $dataString = $this->dataToString($data);

        $this->addQueryPart("UPDATE $tablesString SET $dataString");

        return $this;
    }

    public function deleteFrom($tables) {
        $tablesString = $this->tablesToString($tables);

        $this->addQueryPart("DELETE FROM $tablesString");

        return $this;
    }

    public function where($key, $operator, $value) {
        $this->addCriterion('WHERE', $key, $operator, $value);

        return $this;
    }

    public function _and($key, $operator, $value) {
        $this->addCriterion('AND', $key, $operator, $value);

        return $this;
    }

    public function _or($key, $operator, $value) {
        $this->addCriterion('OR', $key, $operator, $value);

        return $this;
    }

    public function addCriterion($cmd, $key, $operator, $value) {
        $valueFormatted = $this->quoteValue($value);
        $allowedCmds = ['WHERE', 'AND', 'OR'];
        $cmdUpperCase = strtoupper($cmd);

        if(in_array($cmdUpperCase, $allowedCmds)) {
            $this->addQueryPart("$cmdUpperCase $key $operator $valueFormatted");
        }

        return $this;
    }

    public function addCriteria(Array $criteria) {
        foreach($criteria as $criterion) {
            $this->addCriterion($criterion[0], $criterion[1], $criterion[2], $criterion[3]);
        }

        return $this;
    }

    public function limit($amount) {
        $this->addQueryPart("LIMIT $amount");

        return $this;
    }

    public function addQueryPart($string) {
        $this->queryParts[] = $string;

        return $this;
    }

    public function assemble() {
        if(count($this->queryParts) > 0) {
            return implode(' ', $this->queryParts).";";
        }
        else {
            return false;
        }
    }

    private function columnsToString($columns, $glue = ",") {
        $string = null;

        if(is_string($columns)) {
            $string = $columns;
        }
        else if(is_array($columns)) {
            $string = implode($glue, $columns);
        }

        return $string;
    }

    private function tablesToString($tables, $glue = ",") {
        $string = null;

        if(is_string($tables)) {
            $string = $tables;
        }
        else if(is_array($tables)) {
            $string = implode($glue, $tables);
        }

        return $string;
    }

    private function dataToString($data, $gluePair = "=", $glueParts = ",") {
        $parts = [];

        foreach($data as $key => $value) {
            $parts[] = $key.$gluePair.$this->quoteValue($value);
        }

        return implode($glueParts, $parts);
    }

    private function dataKeysToString($data, $glue = ",") {
        $keys = array_keys($data);

        return implode($glue, $keys);
    }

    private function dataValuesToString($data, $glue = ",") {
        $values = $this->quoteValues(array_values($data));

        return implode($glue, $values);
    }

    private function quoteValues($values, $quote = "'") {
        $quoted = [];

        foreach($values as $value) {
            $quoted[] = $this->quoteValue($value);
        }

        return $quoted;
    }

    private function quoteValue($value, $quote = "'") {
        if(is_string($value)) {
            return $quote.$value.$quote;
        }
        else {
            return $value;
        }
    }
}

?>