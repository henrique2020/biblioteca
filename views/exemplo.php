<?php

use App\Usuario;

function exibe($array){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

$usuario = new Usuario(null, 'Henrique', 'henrique@email.com', 'Teste@123');
//exibe($usuario->cadastrar());
exibe(Usuario::login('henrique@email.com', 'Teste@123'));

?>