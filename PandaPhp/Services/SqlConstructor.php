<?php

namespace PandaPHP\Services;

class SqlConstructor {
    private $query;
    private $pdo;
    private $method;
    private $DataFormater;
    private $DataChecker;
    private $preparedDatas;
    public static $instance;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->DataFormater = new \PandaPHP\Services\DataFormater();
        $this->DataChecker = new \PandaPHP\Services\DataChecker();
        $this->preparedDatas = array();
        self::$instance = $this;
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
        $data = $this->DataFormater->prepareWhereValues($args);

        if(!$this->DataChecker->isAssociativeArray($args)) {
            throw new \Exception("Associative Array expected, " . gettype($args) . " given");
        }
        $this->setPreparedArgs($data['values']);
        $this->query = $this->query . " WHERE " . $data['column'];
        return $this;
    }

    public function execute()
    {
        $query = $this->pdo->prepare($this->query,array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

        try {
            if($this->method == 'fetch') {
                if(!empty($this->preparedDatas)) {
                    $query->execute($this->preparedDatas);
                } else {
                    $query->execute();
                }
                return $query->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                if(!empty($this->preparedDatas)) {
                    return $query->execute($this->preparedDatas);
                } else {
                    return $query->execute();
                }
            }
        } catch (\PDOException $e) {
            return $e;
        }

    }

    public function setMethod($method)
    {
        return $this->method = $method;
    }

    public function setPreparedArgs($args)
    {
        if(!empty($this->preparedDatas)) {
            $args = array_merge($this->preparedDatas,$args);
        }
        $this->preparedDatas = $args;
    }


    public function getPreparedArgs()
    {
        return $this->preparedDatas;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    static public function getInstance()
    {
        return self::$instance;
    }
}