<?php

use App\Biblioteca\Livro;
use App\Biblioteca\Genero;

$data = json_decode(file_get_contents('php://input'), true);

switch ("{$method} {$path}") {
    case 'GET /api/livro/generos':
        $generos = Genero::listarTodos();
        json_response(array_map(fn($g) => ['id' => $g->id, 'genero' => $g->genero], $generos));
        break;

    case 'POST /api/livro/create':
        if(Livro::buscarPorNome($data['livro'])) {
            json_response(['error' => 'Já existe um livro cadastrado com esse nome.'], 400);
            break;
        }

        $livro = new Livro(null, $data['livro'], $data['descricao'], $data['autor'], $data['data-lancamento']);
        $generos = isset($data['generos']) 
            ? (is_array($data['generos']) 
                ? $data['generos'] 
                : json_decode($data['generos'], true)
            ) : [];
        
        if (!$livro->cadastrar($generos)) {
            json_response(['error' => 'Não foi possível cadastrar o livro.'], 500);
        } else {
            $slug = slugificar($livro->livro);
            json_response(['ok' => true, 'redirect' => "/livro/{$slug}"]);
        }
 
        break;

    case 'PUT /api/livro/update':
        $livro = Livro::buscarPorId($data['id'], 'completo');
        if (!$livro) {
            json_response(['error' => 'Livro não encontrado.'], 404);
            break;
        }

        $livro->livro = $data['livro'];
        $livro->descricao = $data['descricao'];
        $livro->autor = $data['autor'];

        try {
            $livro->dataLancamento = new DateTime($data['data-lancamento']);
        } catch (\Exception $e) {
            json_response(['error' => 'Data inválida.'], 400);
            break;
        }

        $generos = isset($data['generos']) 
            ? (is_array($data['generos']) 
                ? $data['generos'] 
                : json_decode($data['generos'], true)
            ) : [];

        if (!$livro->editar($generos)) {
            json_response(['error' => 'Não foi possível atualizar o livro.'], 500);
        } else {
            $slug = slugificar($livro->livro);
            json_response(['ok' => true, 'redirect' => "/livro/{$slug}"]);
        }

        break;
    
    default:
        json_response(['error' => 'Endpoint de livro não encontrado'], 404);
        break;
}