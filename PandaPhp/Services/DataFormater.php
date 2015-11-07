<?php

namespace PandaPHP\Services;

class DataFormater {

    public function __construct() {}

    public function formatAssociativeValues($args)
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


    public function formatValue($value)
    {
        return '"' . $value . '"';
        // TODO : avoid sql injection and stuff
    }
}