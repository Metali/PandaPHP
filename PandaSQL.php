<?php

class PandaSQL
{
    public $args;
    private $pdo;
    public $table;

    public function __construct($conf = [])
    {
        $pdo = [
            'db' => (isset($conf['db']) && !empty($conf['db'])) ? $conf['db'] : '',
            'host' => (isset($conf['host']) && !empty($conf['host'])) ? $conf['host'] : '127.0.0.1',
            'charset' => (isset($conf['charset']) && !empty($conf['charset'])) ? $conf['charset'] : 'UTF8',
            'user' => (isset($conf['user']) && !empty($conf['user'])) ? $conf['user'] : 'root',
            'password' => (isset($conf['password']) && !empty($conf['password'])) ? $conf['password'] : 'root',
            'table' => (isset($conf['table']) && !empty($conf['table'])) ? $conf['table'] : '',
        ];

        $this->table = $pdo['table'];

        try {
            $this->pdo = new PDO("mysql:dbname={$pdo['db']};host={$pdo['host']};charset={$pdo['charset']}", $pdo['user'], $pdo['password']);
        } catch (PDOException $e) {
            return "PDO error : " . $e;
        }
    }

    public function insert($args)
    {
        if (!is_array($args)) {
            return "Array expected, " . gettype($args) . " given";
        }

        $data = "";
        $this->args = $args;
        if($this->isAssociativeArray($this->args)) {
            // TODO : assoc
        } else {
            $data = implode($this->args,",");
        }

        if(!$this->isTableDefined()) {
            return "table doesn't exist";
        }



    }

    public function findOneBy($args)
    {
        $this->args = $args;
        if (!$this->args) {
            echo "args empty";
            return false;
        }

    }

    public function findBy()
    {

    }

    public function where()
    {

    }

    public function delete()
    {

    }

    public function update()
    {

    }

    /**
     * Test
     */

    private function isAssociativeArray($array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0 ? true : false;
    }

    private function isTableDefined()
    {
        if(!empty($this->table))
        {
            return false;
        }

        return true;
    }

    // TODO :
    // JOIN
    // args


}