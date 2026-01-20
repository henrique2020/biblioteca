<?php

namespace App\Database;

use PDO;
use PDOException;
use Exception;

class PDO_DB extends DB {
    private static ?PDO $instance = null;

    public static function conectar(): PDO {
        if (self::$instance === null) {
            $config = new self();
            
            try {
                $dsn = "mysql:host={$config->host};dbname={$config->dbName};port={$config->port};charset={$config->charset}";
                
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false, // Desativa emulação para performance e segurança real
                ];

                self::$instance = new PDO($dsn, $config->user, $config->pass, $options);
            } catch (PDOException $e) {
                throw new Exception("Erro PDO: {$e->getMessage()}");
            }
        }
        return self::$instance;
    }
}