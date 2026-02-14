<?php

namespace App\DAO;

use App\Biblioteca\Exemplar;

class ExemplarDAO extends BaseDAO {

    private function mapearDadosParaObjeto(array $dados): Exemplar {
        return new Exemplar(
            (int) $dados['id'], (int) $dados['idLivro'], $dados['codigo'], $dados['dataAquisicao'],  $dados['status'], $dados['ativo']
        );
    }

    public function salvar(Exemplar $exemplar): int|bool {
        try {
            if ($exemplar->id) {
                $sql = "UPDATE exemplar 
                        SET idLivro = :idLivro, codigo = :codigo, dataAquisicao = :dataAquisicao, status = :status, ativo = :ativo 
                        WHERE id = :id";
                
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    ':idLivro' => $exemplar->idLivro,
                    ':codigo' => $exemplar->codigo,
                    ':dataAquisicao' => $exemplar->dataAquisicao->format('Y-m-d'),
                    ':status' => $exemplar->status,
                    ':ativo' => $exemplar->ativo,
                    ':id' => $exemplar->id
                ]);
            } else {
                $sql = "INSERT INTO exemplar (idLivro, codigo, dataAquisicao, status, ativo) 
                        VALUES (:idLivro, :codigo, :dataAquisicao, :status, :ativo)";
                
                $stmt = $this->db->prepare($sql);
                $sucesso = $stmt->execute([
                    ':idLivro' => $exemplar->idLivro,
                    ':codigo' => $exemplar->codigo,
                    ':dataAquisicao' => $exemplar->dataAquisicao->format('Y-m-d'),
                    ':status' => $exemplar->status,
                    ':ativo' => $exemplar->ativo,
                ]);
                
                if ($sucesso) { return (int)$this->db->lastInsertId(); }
                return -1;
            }
        } catch (\Exception $e) {
            error_log("Erro ao salvar exemplar: {$e->getMessage()}");
            return -1;
        }
    }

    public function deletar(int $id): bool {
        $sql = "DELETE FROM exemplar WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]) && $stmt->rowCount() > 0;
    }

    public function buscarPorID(int $id): ?Exemplar {
        $sql = "SELECT * FROM exemplar WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $dados = $stmt->fetch();
        
        if (!$dados) {
            return null;
        }

        return $this->mapearDadosParaObjeto($dados);
    }

    public function listarPorLivro(int $idLivro): array {
        $sql = "SELECT * FROM exemplar WHERE idLivro = :idLivro";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idLivro' => $idLivro]);
        
        $exemplares = [];
        while ($dados = $stmt->fetch()) {
            $exemplares[] = $this->mapearDadosParaObjeto($dados);
        }
        
        return $exemplares;
    }
}