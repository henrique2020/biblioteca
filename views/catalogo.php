<?php

use App\Biblioteca\Livro;

$livros = Livro::listarTodos('completo');
foreach($livros as $livro){
    $slug = slugificar($livro->livro);
    echo "Livro: <a href='livro/{$slug}' target='_blanck'>{$livro->livro}</a><br>";
    echo "Autor: {$livro->autor}<br>";
    echo "Data Lançamento: {$livro->dataLancamento->format('Y-m-d')}<br>";
    echo "Exemplares:<br>";
    foreach ($livro->exemplares as $exemplar) {
        echo "- Código: {$exemplar->codigo} ({$exemplar->status} → ";
        rand(0, 1) ? $exemplar->status()->proximo() : $exemplar->status()->anterior();
        echo "{$exemplar->status})<br>";
    }
    echo "Gêneros:<br>";
    foreach ($livro->generos as $genero) {
        echo "- {$genero->genero}<br>";
    }

    echo "<hr>";
}

?>