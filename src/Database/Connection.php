<?php

namespace Fgtas\Database;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DriverManager;

class Connection
{
    private ?DBALConnection $conn = null;


    public function getConnection(): DBALConnection
    {
        if ($this->conn === null) {
            $connectionParams = [
                'dbname' => $_ENV['DB_NAME'],
                'user' => $_ENV['DB_USERNAME'],
                'password' => $_ENV['DB_PASSWORD'],
                'host' => $_ENV['DB_HOST'],
                'driver' => $_ENV['DB_DRIVER'],
            ];

            $this->conn = DriverManager::getConnection($connectionParams);
        }

        return $this->conn;
    }
}