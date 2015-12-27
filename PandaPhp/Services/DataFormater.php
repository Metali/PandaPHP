<?php

namespace PandaPHP\Services;

class DataFormater {

    public function __construct() {}

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

    public function escapeValue($value)
    {
        $newValue = addslashes($value);
        return $newValue;
    }

    public function addCharIfContinue($char,$index,$max)
    {
        if($index < $max) {
            return $char;
        } else {
            return "";
        }
    }
}