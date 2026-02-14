<?php

namespace App\DAO;

use App\Database\PDO_DB;
use PDO;

abstract class BaseDAO {
    protected PDO $db;

    public function __construct() {
        $this->db = PDO_DB::conectar();
    }

    public function beginTransaction(): void {
        $this->db->beginTransaction();
    }

    public function commit(): void {
        $this->db->commit();
    }

    public function rollBack(): void {
        $this->db->rollBack();
    }
}
