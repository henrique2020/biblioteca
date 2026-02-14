<?php

namespace App\Biblioteca;

use App\DAO\GeneroDAO;

class Genero {

    public function __construct(
        private ?int $id, 
        private string $genero
    ) {}

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    private static function vincularGeneros(Livro $livro, array $idsAdicionar): void {
        $dao = new GeneroDAO();
        foreach ($idsAdicionar as $idGenero) {
            if (!$dao->vincularLivro($idGenero, $livro->id)) {
                throw new \Exception("Erro ao vincular gênero ID: {$idGenero}");
            }
        }
    }

    private static function desvincularGeneros(Livro $livro, array $idsRemover): void {
        $dao = new GeneroDAO();
        foreach ($idsRemover as $idGenero) {
            if (!$dao->desvincularLivro($idGenero, $livro->id)) {
                throw new \Exception("Erro ao desvincular gênero ID: {$idGenero}");
            }
        }
    }

    public function cadastrar(): bool {
        $dao = new GeneroDAO();
        $this->id = $dao->salvar($this);
        return $this->id !== -1;
    }

    public static function buscarPorId(int $id): ?Genero {
        $dao = new GeneroDAO();
        return $dao->buscarPorID($id);
    }

    public static function listarPorLivro(int $livroId): array {
        $dao = new GeneroDAO();
        return $dao->listarPorLivro($livroId);
    }

    public static function listarTodos(): array {
        $dao = new GeneroDAO();
        return $dao->listarTodos();
    }

    public static function definirVinculos(Livro $livro, array $generosNovos): void {
        // Extrai os IDs dos gêneros atuais
        $idsAtuais = array_map(fn($g) => $g->id, $livro->generos);
        
        // Cadastra gêneros novos (com ID NULL) e coleta todos os IDs
        $idsNovos = [];
        foreach ($generosNovos as $genero) {
            if (empty($genero['id'])) {
                $objG = new Genero(null, trim($genero['genero'] ?? ''));
                if (!$objG->cadastrar()) {
                    throw new \Exception("Erro ao cadastrar gênero: {$genero['genero']}");
                }
                $idsNovos[] = $objG->id;
            } else {
                $idsNovos[] = (int)$genero['id'];
            }
        }
        
        // Calcula as operações necessárias
        $remover = array_diff($idsAtuais, $idsNovos);
        $adicionar = array_diff($idsNovos, $idsAtuais);

        self::desvincularGeneros($livro, $remover);
        self::vincularGeneros($livro, $adicionar);
    }
}
