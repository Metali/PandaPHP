<?php

class PandaSQL
{
    public $args;
    public $table;
    private $pdo;
    private $timestart;

    public function __construct($conf = [])
    {
        $this->timestart=microtime(true);

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
        if (!is_array($args)) {
            return new Exception("Array expected, " . gettype($args) . " given");
        }

        $val = [];

        $this->isTableDefined();
        $this->args = $args;

        if ($this->isAssociativeArray($this->args)) {
            $col = [];
            foreach ($this->args as $key => $value) {
                $col[] = $key;
                $val[] = '"' . $value . '"';
            }

            $val = implode($val, ",");
            $col = implode($col, ",");

            $row = "(". $col ." ) VALUES (" . $val . ")";

        } else {
            foreach ($this->args as $key => $value) {
                $val[$key] = '"' . $value . '"';
            }
            $val = implode($val, ",");
            $row = " VALUES (" . $val . ")";
        }

        $sql = $this->pdo->prepare("INSERT INTO " . $this->table . $row);
        return $sql->execute();
    }

    public function findOneBy($args)
    {

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

    /*
    * Test
    */
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

    public function timeend()
    {
        $end = microtime(true);
        $time = $end - $this->timestart;
        $page_load_time = number_format($time, 10);
        echo "<br>Executed " . $page_load_time . " sec";
    }

    // TODO :
    // JOIN
    // args


}