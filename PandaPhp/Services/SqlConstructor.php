<?php

namespace PandaPHP\Services;


class SqlConstructor {
    private $query;
    private $pdo;
    private $method;
    private $DataFormater;
    private $DataChecker;
    
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->DataFormater = new \PandaPHP\Services\DataFormater();
        $this->DataChecker = new \PandaPHP\Services\DataChecker();
    }

    public function limit($limit, $offset = 0)
    {
        $this->DataChecker->isInt($limit);
        $this->DataChecker->isInt($offset);
        $this->query = $this->query . " LIMIT " . $offset . "," . $limit;
        return $this;
    }

    public function where($args)
    {
        $this->DataChecker->isArgsArray($args);
        $row = $this->DataFormater->formatKeyWithValue($args);

        if(!$this->DataChecker->isAssociativeArray($args)) {
            throw new \Exception("Associative Array expected, " . gettype($args) . " given");
        }

        $this->query = $this->query . " WHERE " . $row;
        return $this;
    }

    public function execute()
    {


        if($this->method == 'fetch') {
            if(!is_array($this->query)) {
                $this->query = $this->pdo->query($this->query);
            }

            try {
                return $this->query->fetchAll();
            } catch (\PDOException $e) {
                return $e;
            }
        } else {
            if(!is_array($this->query)) {
                $this->query = $this->pdo->prepare($this->query);
            }

            try {
                return $this->query->execute();
            } catch (\PDOException $e) {
                return $e;
            }
        }

    }

    public function setMethod($method)
    {
        return $this->method = $method;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }
}