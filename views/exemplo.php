<?php

use App\Email\Email;
use App\Usuario;

function exibe($array){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

//$usuario = new Usuario(null, 'Henrique', 'henrique@email.com', 'Teste@123');
//exibe($usuario->cadastrar());
//exibe(Usuario::login('henrique@email.com', 'Teste@123'));

$fila = [];

$data = [
    'assunto' => 'Bem-vindo à Biblioteca!',
    'enviar_para' => [
        'from' => [['email' => 'email@email.com', 'nome' => 'João Silva']],
        'cc' => [],
        'cco' => []
    ],
    'atributos' => [
        'nome' => 'João Silva',
        'data_cadastro' => date('d/m/Y'),
        'link' => 'http://localhost:8000/confirmar-cadastro?token=abc123',
    ],
    'template' => 'cadastro'
];
$fila[] = $data;

$data = [
    'assunto' => 'Redefinição de Senha',
    'enviar_para' => [
        'from' => [['email' => 'email@email.com', 'nome' => 'João Silva']],
        'cc' => [],
        'cco' => []
    ],
    'atributos' => [
        'nome' => 'João Silva',
        'validade' => '30 minutos',
        'link' => 'http://localhost:8000/resetar-senha?token=def456',
    ],
    'template' => 'redefine_senha'
];
$fila[] = $data;

$email = new Email();
foreach($fila as $data){
    $email->enviar($data);
    echo "<hr>";
}
?>