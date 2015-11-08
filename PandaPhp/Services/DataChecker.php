<?php

namespace PandaPHP\Services;

class DataChecker {
    public function __construc() {}
    
    public function isAssociativeArray($array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0 ? true : false;
    }

    public function isArgsArray($args)
    {
        if (!is_array($args)) {
            throw new \Exception("Array expected, " . gettype($args) . " given");
        }
    }

    public function isTableDefined($table)
    {
        if (empty($table)) {
            throw new \Exception('No table defined');
        }
    }
}