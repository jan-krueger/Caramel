<?php

namespace Caramel\Core;

class Database 
{   

    private \PDO $pdo;

    public function __construct($config, $username, $password)
    {
        $dsn = "mysql:" . http_build_query($config, '', ';');
        $this->pdo = new \PDO($dsn, $username, $password, [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }

    public function execute(string $query, array $bindings) 
    {
        $stmt = $this->pdo->prepare($query);
        
        if($stmt->execute($bindings))
        {
            return $stmt;
        }   

        return null;
    }

    /**
     * @return Array, or false if the query failed.
     */
    public function query(string $query, array $bindings = [])
    {

        if($stmt = $this->execute($query, $bindings))
        {
            return $stmt->fetchAll();    
        }

        return false;
    }

    public function queryOne(string $query, array $bindings = [])
    {

        if($stmt = $this->execute($query, $bindings))
        {
            return $stmt->fetch();    
        }

        return false;
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

}