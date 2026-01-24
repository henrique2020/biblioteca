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
        private DateTime|string $dataNascimento,
        private bool $ativo = false,
        private DateTime|string $dataCadastro = 'now'
    ) {
        $this->dataNascimento = (is_string($dataNascimento)) ? new DateTime($dataNascimento) : $dataNascimento;
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
            json_response(['error' => $seguranca]);
            return false;
        }

        return true;
    }

    public static function buscarPorID(int $id): ?Usuario {
        $dao = new UsuarioDAO();
        return $dao->buscarPorID($id);
    }

    public static function buscarPorEmail(string $email): ?Usuario {
        $dao = new UsuarioDAO();
        return $dao->buscarPorEmail($email);
    }

    public function cadastrar(): int {
        $dao = new UsuarioDAO();
        if ($dao->buscarPorEmail($this->email)) {
            json_response(['error' => 'E-mail já cadastrado']);
            return -1;
        }

        if ($this->validaSenhaSegura()) {
            $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);
            return $dao->salvar($this);
        }
        
        return -1;
    }

    public function editar(bool $trocaSenha): int {
        $dao = new UsuarioDAO();

        if($this->id === null) {
            json_response(['error' => 'Usuário não especificado'], 400);
            return -1;
        }

        $dbID = $dao->buscarPorID($this->id);
        $dbEmail = $dao->buscarPorEmail($this->email);
        if($dbID === null) {
            json_response(['error' => 'Usuário não encontrado'], 400);
            return -1;
        }

        if ($dbEmail !== null && $dbEmail->id !== $this->id) {
            json_response(['error' => 'Este e-mail já existe']);
            return -1;
        }

        if (!$trocaSenha || $this->validaSenhaSegura()) {
            $this->senha = $trocaSenha ? password_hash($this->senha, PASSWORD_DEFAULT) : $dbID->senha;
            return $dao->salvar($this);
        }

        return -1;
    }

    public static function estaLogado(): bool {
        $token = $_SESSION['token'] ?? JwtService::getBearerToken();
        $dados = JwtService::validate($token ?? '');

        return $dados !== null;
    }

    public static function dados(): void {
        if (!self::estaLogado()) {
            json_response(['error' => 'Token inválido ou expirado'], 401);
        }

        $token = $_SESSION['token'] ?? JwtService::getBearerToken();
        $payload = JwtService::validate($token ?? '');

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
            $_SESSION['token'] = $token;
            exec("<script>localStorage.removeItem('token');</script>");
            json_response(['ok' => true, 'token' => $token, 'redirect' => '/']);
        } else {
            json_response(['ok' => false, 'error' => 'Usuário e/ou senha inválidos']);
        }
    }

    public static function logout(): void {
        unset($_SESSION['token']);
        exec("<script>localStorage.removeItem('token');</script>");
        json_response(['ok' => true]);
    }
}
