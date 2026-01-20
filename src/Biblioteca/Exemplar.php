<?php

namespace App\Biblioteca;

use DateTime;

class Exemplar {
    public const DISPONIVEL = 'D';  // Disponível
    public const EMPRESTADO = 'E';  // Emprestado
    public const RESERVADO  = 'R';  // Reservado

    public function __construct(
        private ?int $id,
        private int $idCatalogo,
        private string $codigo,
        private DateTime|string $dataAquisicao,
        private string $status = self::DISPONIVEL,
        private bool $ativo = true
    ) {
        $this->dataAquisicao = (is_string($dataAquisicao)) ? new DateTime($dataAquisicao) : $dataAquisicao;
        $this->dataAquisicao->setTime(0, 0, 0);
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        // Permite acesso como propriedade: $exemplar->status->proximo()
        if ($property === 'status') {
            return $this->status();
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    public function status_descritivo(): string {
        return match ($this->status) {
            self::DISPONIVEL => 'Disponível',
            self::EMPRESTADO => 'Emprestado',
            self::RESERVADO => 'Reservado',
            default => 'Status desconhecido',
        };
    }

    private function troca_status(string $direcao): void {
        $map = [
            'avancar' => [
                self::DISPONIVEL => self::RESERVADO,
                self::RESERVADO => self::EMPRESTADO,
                self::EMPRESTADO => self::DISPONIVEL,
            ],
            'voltar' => [
                self::DISPONIVEL => self::EMPRESTADO,
                self::EMPRESTADO => self::RESERVADO,
                self::RESERVADO => self::DISPONIVEL,
            ],
        ];

        if (isset($map[$direcao][$this->status])) {
            $this->status = $map[$direcao][$this->status];
        }
    }

    public function status(): object {
        $avancar = fn() => $this->troca_status('avancar');
        $voltar  = fn() => $this->troca_status('voltar');

        return new class($this, $avancar, $voltar) {
            public function __construct(
                private Exemplar $instance,
                private \Closure $avancar,
                private \Closure $voltar
            ) {}

            public function proximo(): Exemplar {
                ($this->avancar)();
                return $this->instance;
            }

            public function anterior(): Exemplar {
                ($this->voltar)();
                return $this->instance;
            }

            public function __toString(): string {
                return $this->instance->status;
            }
        };
    }
}