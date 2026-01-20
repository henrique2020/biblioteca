<?php

namespace App\Database;

abstract class DB {
    protected string $host;
    protected string $dbName;
    protected string $user;
    protected string $pass;
    protected int $port;
    protected string $charset = 'utf8mb4';

    /**
     * O construtor centraliza a captura das variáveis do $_ENV.
     * Ao ser chamado pelas filhas, garante que os dados estejam prontos.
     */
    public function __construct() {
        $this->host   = $_ENV['DB_HOST'] ?? 'localhost';
        $this->dbName = $_ENV['DB_DATABASE'] ?? 'biblioteca';
        $this->user   = $_ENV['DB_USERNAME'] ?? 'root';
        $this->pass   = $_ENV['DB_PASSWORD'] ?? '';
        $this->port   = (int)($_ENV['DB_PORT'] ?? 3306);
    }

    abstract public static function conectar();
}