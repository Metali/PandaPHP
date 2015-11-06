<?php

class PandaSQL
{
    public $args;
    public $table;
    private $pdo;
    private $lastQuery;

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

    public function create($args)
    {
        $this->args = $args;
        $this->isArgsArray();
        $val = [];

        if ($this->isAssociativeArray($this->args)) {
            $formatedArgs = $this->formatAssociativeValues($this->args);
            $row = " (" . $formatedArgs['col'] . " ) VALUES (" . $formatedArgs['val'] . ")";
        } else {
            foreach ($this->args as $key => $value) {
                $val[$key] = '"' . $value . '"';
            }
            $val = implode($val, ",");
            $row = " VALUES (" . $val . ")";
        }

        $this->lastQuery = $this->pdo->prepare("INSERT INTO " . $this->table . $row);
        return $this->lastQuery;
    }

    public function findOneBy($args)
    {

    }

    public function findBy()
    {

    }

    public function update()
    {

    }

    public function delete($args)
    {
        $this->args = $args;

        $this->isTableDefined();
        $this->isArgsArray($args);


        $row = "DELETE FROM " . $this->table. " WHERE ";
        $i = 1;
        foreach ($this->args as $key => $value) {
            $row .= $key . ' = "' . $value . '"';
            if($i < count($this->args)) {
                $row .= ",";
            }
        }

        $this->lastQuery = $this->pdo->prepare($row);
        return $this->lastQuery;
    }

    /* test */

    public function formatAssociativeValues($args)
    {
        $col = [];
        $val = [];

        foreach ($this->args as $key => $value) {
            $col[] = $key;
            $val[] = '"' . $value . '"';
        }

        $val = implode($val, ",");
        $col = implode($col, ",");

        return ['col' => $col, 'val' => $val];
    }

    public function execute()
    {
        $this->isTableDefined();
        return $this->lastQuery->execute();
    }

    public function getLastQuery()
    {
        return $this->lastQuery;
    }

    private function isAssociativeArray($array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0 ? true : false;
    }

    private function isTableDefined()
    {
        if (empty($this->table)) {
            throw new Exception('No table defined');
        }
    }

    private function isArgsArray()
    {
        if (!is_array($this->args)) {
            throw new Exception("Array expected, " . gettype($this->args) . " given");
        }
    }

    // TODO :
    // JOIN
    // args


}