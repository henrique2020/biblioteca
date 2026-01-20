<?php

use App\Usuario;
$data = json_decode(file_get_contents('php://input'), true);

switch ("{$method} {$path}") {
    // Autenticação
    case 'POST /api/login':
        Usuario::login($data['email'], $data['senha']);
        break;

    // CRUD de Usuário
    case 'POST /api/register':
        $usuario = new Usuario(null, $data['nome'], $data['email'], $data['senha']);
        $usuario->cadastrar();
        break;

    case 'GET /api/user':
        Usuario::dados();
        break;

    case 'PUT /api/user':
        $usuario = new Usuario($data['id'], $data['nome'], $data['email'], $data['senha'], $data['ativo']);
        $usuario->editar();
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint de usuário não encontrado']);
        break;
}