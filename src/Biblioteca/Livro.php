<?php

namespace App\Biblioteca;

use App\Biblioteca\Exemplar;
use App\Biblioteca\Genero;
use App\DAO\LivroDAO;
use DateTime;

class Livro {
    private array $exemplares = [];  // 1..N Biblioteca\Exemplares
    private array $generos = [];     // 1..N Biblioteca\Generos

    public function __construct(
        private ?int $id,
        private string $livro,
        private string $descricao,
        private string $autor,
        private DateTime|string $dataLancamento
    ) {
        $this->dataLancamento = (is_string($dataLancamento)) ? new DateTime($dataLancamento) : $dataLancamento;
        $this->dataLancamento->setTime(0, 0, 0);
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            if ($property === 'dataLancamento' && is_string($value)) {
                $this->$property = new DateTime($value);
            } else {
                $this->$property = $value;
            }
        }
    }

    public function add(object $objeto): self {
        if ($objeto instanceof Exemplar) {
            $this->exemplares[] = $objeto;
        } elseif ($objeto instanceof Genero) {
            if (!in_array($objeto, $this->generos, true)) {
                $this->generos[] = $objeto;
            }
        } else {
            throw new \InvalidArgumentException("Objeto não mapeado: " . get_class($objeto));
        }
        return $this;
    }

    public function cadastrar(array $generos = []): bool {
        $dao = new LivroDAO();       

        try {
            $dao->beginTransaction();

            // Salva o livro
            $this->id = $dao->salvar($this);
            if ($this->id === -1) {
                throw new \Exception("Erro ao salvar o livro");
            }

            Genero::definirVinculos($this, $generos);
            $this->generos = Genero::listarPorLivro($this->id);

            // Confirma a transação
            $dao->commit();
            return true;
            
        } catch (\Exception $e) {
            // Desfaz tudo em caso de erro
            $dao->rollBack();
            error_log("Erro ao cadastrar livro: {$e->getMessage()}");
            $this->id = -1;
            return false;
        }
    }

    public function editar(array $generos = []): bool {
        $dao = new LivroDAO();

        try {
            $dao->beginTransaction();

            $dao->salvar($this);
            Genero::definirVinculos($this, $generos);
            $this->generos = Genero::listarPorLivro($this->id);

            // Confirma a transação
            $dao->commit();
            return true;
            
        } catch (\Exception $e) {
            // Desfaz tudo em caso de erro
            $dao->rollBack();
            error_log("Erro ao editar livro: {$e->getMessage()}");
            return false;
        }
    }
    
    public static function buscarPorID(int $id, string $contexto = 'simples'): ?Livro {
        $dao = new LivroDAO();
        if($contexto === 'simples'){
            return $dao->buscarPorID($id);
        } elseif($contexto === 'completo'){
            return $dao->buscaRecursivaPorID($id);
        } else {
            throw new \InvalidArgumentException("Contexto inválido: {$contexto}");
        }
    }

    public static function buscarPorNome(string $nome, string $contexto = 'simples'): ?Livro {
        $dao = new LivroDAO();
        if($contexto === 'simples'){
            return $dao->buscarPorNome($nome);
        } elseif($contexto === 'completo'){
            return $dao->buscaRecursivaPorNome($nome);
        } else {
            throw new \InvalidArgumentException("Contexto inválido: {$contexto}");
        }
    }

    public static function buscarPorSlug(string $slug, string $contexto = 'simples'): ?Livro {
        $dao = new LivroDAO();
        if($contexto === 'simples'){
            return $dao->buscarPorSlug($slug);
        } elseif($contexto === 'completo'){
            return $dao->buscaRecursivaPorSlug($slug);
        } else {
            throw new \InvalidArgumentException("Contexto inválido: {$contexto}");
        }
    }

    public static function listarTodos(string $contexto = 'simples'): array {
        $dao = new LivroDAO();
        if($contexto === 'simples'){
            return $dao->listarTodos();
        } elseif($contexto === 'completo'){
            return $dao->listaRecursiva();
        } else {
            throw new \InvalidArgumentException("Contexto inválido: {$contexto}");
        }
    }
}
