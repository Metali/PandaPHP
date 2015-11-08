<?php
namespace PandaPHP;

require("Services/DataFormater.php");
require("Services/DataChecker.php");
require("Services/SqlConstructor.php");

use PandaPHP\Services;

class Panda
{
    /** @var (String) $table : current table used  */
    public $table;
    /** @var Services\DataFormater */
    private $DataFormater;
    /** @var Services\DataChecker  */
    private $DataChecker;
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
        $this->DataChecker = new \PandaPHP\Services\DataChecker();
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

    public function create($args)
    {
        $this->DataChecker->isArgsArray($args);
        $this->DataChecker->isTableDefined($this->table);

        $val = [];

        if ($this->DataChecker->isAssociativeArray($args)) {
            $formatedArgs = $this->DataFormater->formatValueForInsert($args);
            $row = " (" . $formatedArgs['col'] . " ) VALUES (" . $formatedArgs['val'] . ")";
        } else {
            foreach ($args as $key => $value) {
                $val[$key] = $this->DataFormater->formatValue($value);
            }

            $val = implode($val, ",");
            $row = " VALUES (" . $val . ")";
        }

        $this->SqlConstructor->setQuery("INSERT INTO " . $this->table . $row);
        return $this->SqlConstructor;
    }

    public function find($args = [])
    {
        $this->DataChecker->isArgsArray($args);
        $this->DataChecker->isTableDefined($this->table);

        if($this->DataChecker->isAssociativeArray($args)) {
            throw new \Exception("Numeric array expected, associative array given");
        }

        if(empty($args)) {
           $row = "*";
        } else {
            $row = implode($args, ",");
        }

        $this->SqlConstructor->setQuery("SELECT " . $row . " FROM " . $this->table);
        return $this->SqlConstructor;
    }

    public function update($args)
    {
        $this->DataChecker->isArgsArray($args);
        $this->DataChecker->isTableDefined($this->table);

        $row = $this->DataFormater->formatKeyWithValue($args);
        $this->SqlConstructor->setQuery("UPDATE " . $this->table . " SET " . $row);
        return $this->SqlConstructor;
    }

    public function delete()
    {
        $this->DataChecker->isTableDefined($this->table);

        $this->SqlConstructor->setQuery("DELETE FROM " . $this->table);
        return $this->SqlConstructor;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function getLastQuery()
    {
        return $this->SqlConstructor->getQuery();
    }

    // TODO
    // JOIN
}