<?php

namespace App\Biblioteca;

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
}
