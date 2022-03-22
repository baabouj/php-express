<?php

namespace Pexess\Database;

class Database
{
    public \PDO $pdo;

    private \PDOStatement|false $statement;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->pdo = new \PDO($_ENV["DB_DSN"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function query($sql)
    {
        $this->statement = $this->pdo->prepare($sql);
    }

    public function bind($parameter, $value, $type = null)
    {
        $type = match (is_null($type)) {
            is_int($value) => \PDO::PARAM_INT,
            is_bool($value) => \PDO::PARAM_BOOL,
            is_null($value) => \PDO::PARAM_NULL,
            default => \PDO::PARAM_STR,
        };
        $this->statement->bindValue($parameter, $value, $type);
    }

    public function execute(): bool
    {
        return $this->statement->execute();
    }

    public function resultSet(): bool|array
    {
        $this->execute();
        return $this->statement->fetchAll(\PDO::FETCH_OBJ);
    }


    public function single()
    {
        $this->execute();
        return $this->statement->fetch(\PDO::FETCH_OBJ);
    }

    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }
}