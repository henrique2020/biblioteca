<?php

namespace App\DAO;

use App\Biblioteca\Genero;
use App\Database\PDO_DB;
use PDO;

class GeneroDAO {
    private PDO $db;

    public function __construct() {
        $this->db = PDO_DB::conectar();
    }

    private function mapearDadosParaObjeto(array $dados): Genero {
        return new Genero(
            (int) $dados['id'], $dados['genero']
        );
    }

    public function salvar(Genero $genero): int|bool {
        try {
            if ($genero->id) {
                $sql = "UPDATE genero 
                        SET genero = :genero 
                        WHERE id = :id";
                
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    ':genero' => $genero->genero,
                    ':id' => $genero->id
                ]);
            } else {
                $sql = "INSERT INTO genero (genero) 
                        VALUES (:genero)";
                
                $stmt = $this->db->prepare($sql);
                $sucesso = $stmt->execute([
                    ':genero' => $genero->genero,
                ]);
                
                if ($sucesso) { return (int)$this->db->lastInsertId(); }
                return -1;
            }
        } catch (\Exception $e) {
            error_log("Erro ao salvar genêro: {$e->getMessage()}");
            return -1;
        }
    }

    public function deletar(int $id): bool {
        $sql = "DELETE FROM genero WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]) && $stmt->rowCount() > 0;
    }

    public function buscarPorID(int $id): ?Genero {
        $sql = "SELECT * FROM genero WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $dados = $stmt->fetch();
        
        if (!$dados) {
            return null;
        }

        return $this->mapearDadosParaObjeto($dados);
    }

    public function listarPorLivro(int $idLivro): array {
        $sql = "SELECT g.* 
                FROM genero g
                JOIN livro_genero lg ON lg.idGenero = g.id
                WHERE lg.idLivro = :idLivro";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idLivro' => $idLivro]);
        
        $generoes = [];
        while ($dados = $stmt->fetch()) {
            $generoes[] = $this->mapearDadosParaObjeto($dados);
        }
        return $generoes;
    }
}