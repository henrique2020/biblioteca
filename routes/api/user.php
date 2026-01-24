<?php

use App\Usuario;
$data = json_decode(file_get_contents('php://input'), true);

switch ("{$method} {$path}") {
    // Autenticação
    case 'POST /api/login':
        Usuario::login($data['email'], $data['senha']);
        break;
    
    case 'POST /api/logout':
        Usuario::logout();
        break;
    
    // Rotas de usuário
    case 'GET /api/user':
        Usuario::dados();
        break;

    case 'POST /api/user/create':
        if($data['senha'] !== $data['confirmacao-senha']) {
            json_response(['error' => 'As senhas não coincidem']);
        }

        $nome = "{$data['nome']} {$data['sobrenome']}";
        $usuario = new Usuario(null, $nome, $data['email'], $data['senha'], $data['data-nascimento']);
        $id = $usuario->cadastrar();

        if($id > 0){ json_response(['ok' => true]); } 
        else { json_response(['error' => 'Não foi possível cadastrar o usuário'], 500); }
        break;

    case 'PUT /api/user/update':
        $usuario = new Usuario($data['id'], $data['nome'], $data['email'], $data['senha'], $data['data-nascimento']);
        $alteracoes = $usuario->editar(!empty($data['senha']));

        if($alteracoes > 0){ json_response(['ok' => true]); } 
        else { json_response(['error' => 'Não foi possível editar o usuário'], 500); }
        break;

    default:
        json_response(['error' => 'Endpoint de usuário não encontrado'], 404);
        break;
}