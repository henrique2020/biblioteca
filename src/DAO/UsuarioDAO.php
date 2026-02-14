<?php

namespace App\DAO;

use App\Usuario;
use Exception;

class UsuarioDAO extends BaseDAO {

    private function mapearDadosParaObjeto(array $dados): Usuario {
        return new Usuario(
            (int) $dados['id'], $dados['nome'], $dados['email'], $dados['senha'], $dados['dataNascimento'], $dados['ativo'], $dados['dataCadastro']
        );
    }

    public function salvar(Usuario $usuario): int|bool {
        try {
            if ($usuario->id) {
                $sql = "UPDATE usuario 
                        SET nome = :nome, email = :email, senha = :senha, dataNascimento = :dataNascimento, ativo = :ativo 
                        WHERE id = :id";
                
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    ':nome' => $usuario->nome,
                    ':email' => $usuario->email,
                    ':senha' => $usuario->senha,
                    ':dataNascimento' => $usuario->dataNascimento->format('Y-m-d'),
                    ':ativo' => $usuario->ativo,
                    ':id' => $usuario->id
                ]);
            } else {
                $sql = "INSERT INTO usuario (nome, email, senha, dataNascimento, ativo, dataCadastro) 
                        VALUES (:nome, :email, :senha, :dataNascimento, :ativo, :dataCadastro)";
                
                $stmt = $this->db->prepare($sql);
                $sucesso = $stmt->execute([
                    ':nome' => $usuario->nome,
                    ':email' => $usuario->email,
                    ':senha' => $usuario->senha,
                    ':dataNascimento' => $usuario->dataNascimento->format('Y-m-d'),
                    ':ativo' => $usuario->ativo,
                    ':dataCadastro' => $usuario->dataCadastro->format('Y-m-d H:i:s')
                ]);
                
                if ($sucesso) { return (int)$this->db->lastInsertId(); }
                return -1;
            }
        } catch (\Exception $e) {
            error_log("Erro ao salvar usuario: {$e->getMessage()}");
            return -1;
        }
    }

    /**
     * Função privada buscar um usuario específico
     * Recebe a cláusula buscaada e os parâmetros correspondentes
     * 
     * @param string $where Cláusula WHERE da consulta
     * @param array $parametros Array de parâmetros (ex: [':id' => 5])
     * @return ?Livro Retorna o livro ou null se não encontrado
     */
    private function buscar(string $where, array $parametros = []): ?Usuario {
        $stmt = $this->db->prepare("SELECT * FROM usuario WHERE {$where}");
        $stmt->execute($parametros);
        
        $dados = $stmt->fetch();
        
        if (!$dados) {
            return null;
        }

        return $this->mapearDadosParaObjeto($dados);
    }
    
    public function buscarPorID(int $id): ?Usuario {
        return $this->buscar("id = :id", [':id' => $id]);
    }

    public function buscarPorEmail(string $email): ?Usuario {
        return $this->buscar("email = :email", [':email' => $email]);
    }
}