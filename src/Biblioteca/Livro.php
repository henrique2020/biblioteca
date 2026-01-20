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
            $this->$property = $value;
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
    
    public static function buscarPorID(int $id, string $contexto = 'simples'): ?Livro {
        $dao = new LivroDAO();
        if($contexto === 'simples'){
            return $dao->buscarPorID($id);
        } elseif($contexto === 'completo'){
            return $dao->buscaRecursivaPorID($id);
        } else {
            throw new \InvalidArgumentException("Contexto inválido: " . $contexto);
        }
    }

    public static function buscarPorNome(string $nome, string $contexto = 'simples'): ?Livro {
        $dao = new LivroDAO();
        if($contexto === 'simples'){
            return $dao->buscarPorNome($nome);
        } elseif($contexto === 'completo'){
            return $dao->buscaRecursivaPorNome($nome);
        } else {
            throw new \InvalidArgumentException("Contexto inválido: " . $contexto);
        }
    }

    public static function buscarPorSlug(string $slug, string $contexto = 'simples'): ?Livro {
        $dao = new LivroDAO();
        if($contexto === 'simples'){
            return $dao->buscarPorSlug($slug);
        } elseif($contexto === 'completo'){
            return $dao->buscaRecursivaPorSlug($slug);
        } else {
            throw new \InvalidArgumentException("Contexto inválido: " . $contexto);
        }
    }

    public static function listarTodos(string $contexto = 'simples'): array {
        $dao = new LivroDAO();
        if($contexto === 'simples'){
            return $dao->listarTodos();
        } elseif($contexto === 'completo'){
            return $dao->listaRecursiva();
        } else {
            throw new \InvalidArgumentException("Contexto inválido: " . $contexto);
        }
    }
}
