<?php

namespace PandaPHP;

use PandaPHP\Services;

class Panda
{
    /** @var (String) $table : current table used  */
    public $table;
    /** @var (Array) $args : current args for SQL request*/
    private $args;
    /** @var Services\DataFormater */
    private $DataFormater;
    /** @var Services\SqlConstructor */
    private $SqlConstructor;

    /**
     * (Array) @param $conf
     * construct with SQL configuration for PDO connect
     * Can take the 'table' param to avoid futur errors
     */
    public function __construct($conf = [])
    {
        $this->DataFormater = new \PandaPHP\Services\DataFormater();

        $pdo = [
            'db' => (isset($conf['db']) && !empty($conf['db'])) ? $conf['db'] : '',
            'host' => (isset($conf['host']) && !empty($conf['host'])) ? $conf['host'] : '127.0.0.1',
            'charset' => (isset($conf['charset']) && !empty($conf['charset'])) ? $conf['charset'] : 'UTF8',
            'user' => (isset($conf['user']) && !empty($conf['user'])) ? $conf['user'] : 'root',
            'password' => (isset($conf['password']) && !empty($conf['password'])) ? $conf['password'] : 'root',
        ];

        try {
            $pdo = new \PDO("mysql:dbname={$pdo['db']};host={$pdo['host']};charset={$pdo['charset']}", $pdo['user'], $pdo['password']);
            $this->SqlConstructor = new \PandaPHP\Services\SqlConstructor($pdo);
        } catch (\PDOException $e) {
            return "PDO error : " . $e;
        }
    }

    /**
     * @param (Array)$args
     * @return PDOStatement
     * @throws Exception
     */
    public function create($args)
    {
        $this->args = $args;
        $this->isArgsArray();
        $val = [];

        if ($this->isAssociativeArray($this->args)) {
            $formatedArgs = $this->DataFormater->formatAssociativeValues($this->args);
            $row = " (" . $formatedArgs['col'] . " ) VALUES (" . $formatedArgs['val'] . ")";
        } else {
            foreach ($this->args as $key => $value) {
                $val[$key] = $this->DataFormater->formatValue($value);
            }

            $val = implode($val, ",");
            $row = " VALUES (" . $val . ")";
        }

        $this->SqlConstructor->setQuery("INSERT INTO " . $this->table . $row);
        return $this->SqlConstructor;
    }

    public function findBy()
    {
        // TODO
    }

    public function update()
    {
        /*
            TODO

            UPDATE table
            SET colonne_1 = 'valeur 1', colonne_2 = 'valeur 2', colonne_3 = 'valeur 3'
            WHERE condition
         */
    }

    public function delete($args)
    {
        $this->args = $args;
        $this->isArgsArray();

        $row = "";
        $i = 1;

        foreach ($this->args as $key => $value) {
            $row .= $key . ' = ' . $this->DataFormater->formatValue($value) . '';
            if($i < count($this->args)) {
                $row .= ",";
            }
        }

        $this->SqlConstructor->setQuery("DELETE FROM " . $this->table. " WHERE ".$row);
        return $this->SqlConstructor;
    }


    private function isAssociativeArray($array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0 ? true : false;
    }

    private function isArgsArray()
    {
        if (!is_array($this->args)) {
            throw new \Exception("Array expected, " . gettype($this->args) . " given");
        }
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function isTableDefined()
    {
        if (empty($this->table)) {
            throw new \Exception('No table defined');
        }
    }

    // TODO
    // JOIN
    // args


}