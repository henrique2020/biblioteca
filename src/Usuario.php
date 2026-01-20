<?php

namespace App;

use App\DAO\UsuarioDAO;
use App\Auth\JwtService;
use DateTime;

class Usuario {

    public function __construct(
        private ?int $id, 
        private string $nome, 
        private string $email,
        private string $senha,
        private bool $ativo = false,
        private DateTime|string $dataCadastro = 'now'
    ) {
        $this->dataCadastro = (is_string($dataCadastro)) ? new DateTime($dataCadastro) : $dataCadastro;
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

    private function validaSenhaSegura(): bool {
        $seguranca = validate_password_strength($this->senha);
        if ($seguranca) {
            json_response(['error' => $seguranca], 400);
            return false;
        }

        return true;
    }

    public function cadastrar(): int {
        $dao = new UsuarioDAO();

        if ($this->validaSenhaSegura()) {
            $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);
            return $dao->salvar($this);
        }
        
        return -1;
    }

    public function editar(): int {
        $dao = new UsuarioDAO();

        if($this->id === null) {
            json_response(['error' => 'Usuário não especificado'], 400);
            return -1;
        }

        if (!$this->validaSenhaSegura()) {
            return -1;
        }
        
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);
        return $dao->salvar($this);
    }

    public static function dados(): void {
        $token = JwtService::getBearerToken();
        $payload = JwtService::validate($token ?? '');

        if (!$payload) {
            json_response(['error' => 'Token inválido ou expirado'], 401);
        }

        $dao = new UsuarioDAO();
        $usuario = get_object_vars($dao->buscarPorEmail($payload->email));
        unset($usuario['senha']);
        json_response($usuario);
    }

    public static function login(string $email, string $senha): void {
        $dao = new UsuarioDAO();
        $usuario = $dao->buscarPorEmail($email);

        if ($usuario && password_verify($senha, $usuario->senha)) {
            $token = JwtService::create(['email' => $usuario->email, 'nome' => $usuario->nome]);
            json_response(['token' => $token]);
        } else {
            json_response(['error' => 'Credenciais inválidas'], 401);
        }

    }
}
