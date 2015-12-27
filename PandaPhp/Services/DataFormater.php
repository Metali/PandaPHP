<?php

namespace PandaPHP\Services;

class DataFormater {

    public function __construct() {}

    /**
     * Format array to build string with each values and keys
     * Return array with col = key,key,key / val = val,val,val
     * @param $args
     * @return array
     */
    public function formatKeyAndValue($args)
    {
        $col = [];
        $val = [];

        foreach ($args as $key => $value) {
            $col[] = $key;
            $val[] = "'".$this->escapeValue($value)."'";
        }

        $val = implode($val, ",");
        $col = implode($col, ",");

        return ['col' => $col, 'val' => $val];
    }

    /**
     * Prepare value for an insert sql request
     * @param $args
     * @return array
     */
    public function prepareInsertValues($args)
    {
        $row = "";
        $temporary = "";
        $preparedValue = array();
        $count = count($args);
        $loop = 1;

        foreach ($args as $key => $value) {
            $row .= $key;
            $row .= $this->addCharIfContinue(',',$loop,$count);

            $temporary .= ':'.$key;
            $temporary .= $this->addCharIfContinue(',',$loop,$count);

            $preparedValue[':'.$key] = $this->escapeValue($value);
            $loop++;
        }

        return ['key' => $row, 'temporary_values' => $temporary, 'prepared_values' => $preparedValue];
    }

    /**
     * Prepare value for a WHERE key = value condition
     * @param $args
     * @return array
     */
    public function prepareWhereValues($args)
    {
        $row = "";
        $preparedValue = array();
        $count = count($args);
        $loop = 1;
        foreach ($args as $key => $value) {
            $row .= $key .' = '. ':'.$key;
            $row .= $this->addCharIfContinue(' AND ',$loop,$count);
            $preparedValue[':'.$key] = $this->escapeValue($value);
            $loop++;
        }

        return ['column' => $row, 'values' => $preparedValue];
    }

    /**
     * Escape value to prevent sql injection
     * @param $value
     * @return string
     */
    public function escapeValue($value)
    {
        $newValue = addslashes($value);
        return $newValue;
    }

    /**
     * Add character at the end of the string if the index is inferior to the max
     * @param $char
     * @param $index
     * @param $max
     * @return string
     */
    public function addCharIfContinue($char,$index,$max)
    {
        if($index < $max) {
            return $char;
        } else {
            return "";
        }
    }

    /**
     * Use PDO->quote function and return the quoted string
     * @param $value
     * @return mixed
     */
    public function quote($value)
    {
        $pdo = \PandaPHP\Services\SqlConstructor::getInstance()->getPdo();
        return $pdo->quote($value);
    }
}