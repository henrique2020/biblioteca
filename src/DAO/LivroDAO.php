<?php

namespace App\DAO;

use App\Biblioteca\Livro;
use App\DAO\ExemplarDAO;
use App\DAO\GeneroDAO;
use App\Database\PDO_DB;
use PDO;

class LivroDAO {
    private PDO $db;

    public function __construct() {
        $this->db = PDO_DB::conectar();
    }

    private function mapearDadosParaObjeto(array $dados): Livro {
        return new Livro(
            (int) $dados['id'], $dados['livro'], $dados['descricao'], $dados['autor'], $dados['dataLancamento']
        );
    }

    public function salvar(Livro $livro): int|bool {
        try {
            if ($livro->id) {   // Atualizar livro existente
                $sql = "UPDATE livro 
                        SET livro = :livro, descricao = :descricao, autor = :autor, dataLancamento = :dataLancamento 
                        WHERE id = :id";
                
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    ':id' => $livro->id,
                    ':livro' => $livro->livro,
                    ':descricao' => $livro->descricao,
                    ':autor' => $livro->autor,
                    ':dataLancamento' => $livro->dataLancamento->format('Y-m-d')
                ]);
            } else {    // Inserir novo livro
                $sql = "INSERT INTO livro (livro, descricao, autor, dataLancamento) 
                        VALUES (:livro, :descricao, :autor, :dataLancamento)";
                
                $stmt = $this->db->prepare($sql);
                $sucesso = $stmt->execute([
                    ':livro' => $livro->livro,
                    ':descricao' => $livro->descricao,
                    ':autor' => $livro->autor,
                    ':dataLancamento' => $livro->dataLancamento->format('Y-m-d')
                ]);
                
                if ($sucesso) { return (int)$this->db->lastInsertId(); }
                return -1;
            }
        } catch (\Exception $e) {
            error_log("Erro ao salvar livro: {$e->getMessage()}");
            return -1; // Retorna -1 em caso de erro na inserção
        }
    }

    public function deletar(int $id): bool {
        $sql = "DELETE FROM livro WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]) && $stmt->rowCount() > 0;
    }

    /**
     * Função privada buscar um livro específico
     * Recebe a cláusula buscaada e os parâmetros correspondentes
     * 
     * @param string $where Cláusula WHERE da consulta
     * @param array $parametros Array de parâmetros (ex: [':id' => 5])
     * @return ?Livro Retorna o livro ou null se não encontrado
     */
    private function buscar(string $where, array $parametros = []): ?Livro {
        $stmt = $this->db->prepare("SELECT * FROM livro WHERE {$where}");
        $stmt->execute($parametros);
        
        $dados = $stmt->fetch();
        
        if (!$dados) {
            return null;
        }

        return $this->mapearDadosParaObjeto($dados);
    }

    public function buscarPorID(int $id): ?Livro {
        return $this->buscar("id = :id", [':id' => $id]);
    }

    public function buscarPorNome(string $nome): ?Livro {
        return $this->buscar("livro = :nome", [':nome' => $nome]);
    }

    public function buscarPorSlug(string $slug): ?Livro {
        $livros = $this->listarTodos();
        
        foreach ($livros as $livro) {
            if (slugificar($livro->livro) === $slug) {
                return $livro;
            }
        }
        
        return null;
    }

    public function listarTodos(): array {
        $sql = "SELECT * FROM livro ORDER BY livro ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $dados = $stmt->fetchAll();
        $livros = [];

        foreach ($dados as $item) {
            $livros[] = $this->mapearDadosParaObjeto($item);
        }

        return $livros;
    }

    public function listarPorAutor(string $autor): array {
        $sql = "SELECT * FROM livro WHERE autor LIKE :autor ORDER BY livro ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':autor' => "%{$autor}%"]);
        
        $dados = $stmt->fetchAll();
        $livros = [];

        foreach ($dados as $item) {
            $livros[] = $this->mapearDadosParaObjeto($item);
        }

        return $livros;
    }

    public function listarPorGenero(string $genero): array {
        $sql = "SELECT DISTINCT l.* FROM livro l
                INNER JOIN livro_genero lg ON l.id = lg.livro_id
                INNER JOIN generos g ON lg.genero_id = g.id
                WHERE g.nome LIKE :genero
                ORDER BY l.livro ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':genero' => "%{$genero}%"]);
        
        $dados = $stmt->fetchAll();
        $livros = [];

        foreach ($dados as $item) {
            $livros[] = $this->mapearDadosParaObjeto($item);
        }

        return $livros;
    }

    /**
     * Busca recursiva de todas as sub-classes de um livro
     */
    public function buscaRecursivaPorID(int $id): ?Livro {
        $livro = $this->buscarPorID($id);
        
        if (!$livro) { return null; }

        $livro->exemplares = (new ExemplarDAO())->listarPorLivro($id);
        $livro->generos = (new GeneroDAO())->listarPorLivro($id);
        return $livro;
    }

    public function buscaRecursivaPorNome(string $nome): ?Livro {
        $livro = $this->buscarPorNome($nome);
        
        if (!$livro) { return null; }

        $livro->exemplares = (new ExemplarDAO())->listarPorLivro($livro->id);
        $livro->generos = (new GeneroDAO())->listarPorLivro($livro->id);
        return $livro;
    }

    public function buscaRecursivaPorSlug(string $slug): ?Livro {
        $livro = $this->buscarPorSlug($slug);
        
        if (!$livro) { return null; }

        $livro->exemplares = (new ExemplarDAO())->listarPorLivro($livro->id);
        $livro->generos = (new GeneroDAO())->listarPorLivro($livro->id);
        return $livro;
    }

    public function listaRecursiva(): array {
        $livros = $this->listarTodos();
        
        foreach ($livros as $livro) {
            $livro->exemplares = (new ExemplarDAO())->listarPorLivro($livro->id);
            $livro->generos = (new GeneroDAO())->listarPorLivro($livro->id);
        }

        return $livros;
    }
}
