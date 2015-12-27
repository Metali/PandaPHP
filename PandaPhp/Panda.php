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

    /**
     * INSERT SQL REQUEST : build a sql insert request
     * @param ARRAY $args :  column in the table = value inserted
     * @return Services\SqlConstructor
     * @throws \Exception
     */
    public function insert($args)
    {
        $this->DataChecker->isArgsArray($args);
        $this->DataChecker->isTableDefined($this->table);
        $this->SqlConstructor->setMethod('execute');

        if(!$this->DataChecker->isAssociativeArray($args)) {
            throw new \Exception("Associativ array expected, numeric array given");
        }

        $preparedValues = $this->DataFormater->prepareInsertValues($args);
        $row = " (" . $preparedValues['key'] . " ) VALUES (" . $preparedValues['temporary_values'] . ")";
        $this->SqlConstructor->setPreparedArgs($preparedValues['prepared_values']);
        $this->SqlConstructor->setQuery("INSERT INTO " . $this->table . $row);

        return $this->SqlConstructor;
    }

    /**
     * SELECT SQL REQUEST : build a sql select request
     * @param ARRAY $args : columns selected
     * @return Services\SqlConstructor
     * @throws \Exception
     */
    public function select($args = [])
    {
        $this->DataChecker->isArgsArray($args);
        $this->DataChecker->isTableDefined($this->table);
        $this->SqlConstructor->setMethod('fetch');

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

    /**
     * UPDATE SQL REQUEST : build an update sql request
     * @param ARRAY $args : column to update => value inserted
     * @return Services\SqlConstructor
     * @throws \Exception
     */
    public function update($args)
    {
        $this->DataChecker->isArgsArray($args);
        $this->DataChecker->isTableDefined($this->table);
        $this->SqlConstructor->setMethod('execute');
        
        $row = $this->DataFormater->prepareWhereValues($args);
        $this->SqlConstructor->setPreparedArgs($row['values']);
        $this->SqlConstructor->setQuery("UPDATE " . $this->table . " SET " . $row['column']);
        return $this->SqlConstructor;
    }

    /**
     * DELETE SQL REQUEST : build an delete sql request
     * @return Services\SqlConstructor
     * @throws \Exception
     */
    public function delete()
    {
        $this->DataChecker->isTableDefined($this->table);
        $this->SqlConstructor->setMethod('execute');

        $this->SqlConstructor->setQuery("DELETE FROM " . $this->table);
        return $this->SqlConstructor;
    }

    /**
     * Set the table used in the next sql instruction
     * @param STRING $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Return last query used
     * @return mixed
     */
    public function getLastQuery()
    {
        return $this->SqlConstructor->getQuery();
    }

    /**
     * SQL REQUEST : make the user able to request his own sql request
     * @param STRING $request
     * @return \Exception|\PDOException
     */
    public function sql($request)
    {
        $this->SqlConstructor->setMethod('fetch');
        $this->SqlConstructor->setQuery($request);
        return $this->SqlConstructor->execute();
    }

    /**
     * Return the DataFormater Class if user need to use its functions
     * @return Services\DataFormater
     */
    public function DataFormater()
    {
        return $this->DataFormater;
    }

    // TODO
    // JOIN
}