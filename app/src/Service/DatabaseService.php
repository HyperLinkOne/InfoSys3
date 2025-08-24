<?php

namespace App\Service;

use PDO;
use PDOException;

class DatabaseService
{
    private PDO $connection;

    public function __construct(private ConfigService $config)
    {
        $dbConfig = $this->config->getDatabaseConfig();
        
        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $db   = $dbConfig['name'];
        $user = $dbConfig['user'];
        $pass = $dbConfig['pass'];

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        try {
            $this->connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new \RuntimeException('DB-Verbindung fehlgeschlagen: ' . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
