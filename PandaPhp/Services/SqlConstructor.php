<?php

namespace PandaPHP\Services;


class SqlConstructor {
    private $query;
    private $pdo;
    private $table;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function setTable($table)
    {
        if(!empty($table)) {
            $this->table = $table;
        }
    }

    public function limit()
    {

    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function execute()
    {

        var_dump($this->table);
        if(!is_array($this->query)) {
            $this->query = $this->pdo->prepare($this->query);
        }

        var_dump($this->query);
        try {
            return $this->query->execute();
        } catch (\PDOException $e) {
            return $e;
        }
    }
}