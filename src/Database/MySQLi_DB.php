<?php

namespace App\Database;

use mysqli;
use Exception;

class MySQLi_DB extends DB {
    private static ?mysqli $instance = null;

    public static function conectar(): mysqli {
        if (self::$instance === null) {
            $config = new self();
            
            \mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            try {
                self::$instance = new mysqli(
                    $config->host, 
                    $config->user, 
                    $config->pass, 
                    $config->dbName, 
                    $config->port
                );

                self::$instance->set_charset($config->charset);

            } catch (Exception $e) {
                throw new Exception("Erro MySQLi: {$e->getMessage()}");
            }
        }
        return self::$instance;
    }
}