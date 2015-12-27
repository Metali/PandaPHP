<?php

namespace PandaPHP\Services;

class DataFormater {

    public function __construct() {}

    public function formatValueForInsert($args)
    {
        $col = [];
        $val = [];

        foreach ($args as $key => $value) {
            $col[] = $key;
            $val[] = $this->formatValue($value);
        }

        $val = implode($val, ",");
        $col = implode($col, ",");

        return ['col' => $col, 'val' => $val];
    }

    public function formatKeyWithValue($args)
    {
        $row = "";
        foreach ($args as $key => $value) {
            $row .= $key .' = '. $this->formatValue($value);
        }

        return $row;
    }

    public function formatValue($value)
    {
        // TODO : avoid sql injection and stuff
        return '"' . mysql_real_escape_string(addslashes($value)) . '"';
    }
}