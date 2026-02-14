<?php

use App\Usuario;

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
route_log();

header('Content-Type: text/html; charset=UTF-8');

if(!Usuario::estaLogado() 
    && !str_starts_with($path, '/api/')
    && $path !== '/'
    && $path !== '/cadastre-se')
{
    header('Location: /');
    exit;
}

//View Routes
if ($path === '/') {
    if(Usuario::estaLogado()) {
        require_once view_path('home.php');
    } else {
        require_once view_path('login.php');
    }
    exit;
} else if ($path === '/cadastre-se') {
    require_once view_path('usuario/cadastro.php');
    exit;
} else if ($path === '/perfil') {
    Usuario::estaLogado();
    require_once view_path('usuario/perfil.php');
    exit;
} else if (str_starts_with($path, '/livro/cadastrar')) {
    require_once view_path('livro/cadastra.php');
    exit;
} else if (str_starts_with($path, '/livro/editar/')) {  // Rota dinâmica: /livro/editar/{id}
    // Extrai o slug da URL
    $id = str_replace('/livro/editar/', '', $path);
    
    $id = explode('?', $id)[0];
    $id = trim($id, '/');
    
    // Se não tiver um id, redireciona para home
    if (empty($id) || !is_numeric($id)) {
        header('Location: /');
        exit;
    }
    
    require_once view_path('livro/edita.php');
    exit;
} else if (str_starts_with($path, '/livro/')) { // Rota dinâmica: /livro/{slug}
    Usuario::estaLogado();
    // Extrai o slug da URL
    $slug = str_replace('/livro/', '', $path);
    
    $slug = explode('?', $slug)[0];
    $slug = trim($slug, '/');
    
    // Se não tiver um slug, redireciona para home
    if (empty($slug)) {
        header('Location: /');
        exit;
    }
    
    require_once view_path('livro/consulta.php');
    exit;
}

header('Content-Type: application/json');
//API Routes
if(str_starts_with($path, '/api/')){
    $dir = __DIR__ . '/api/';
    if(str_ends_with($path, '/user')
        || str_ends_with($path, '/login')
        || str_ends_with($path, '/logout')
        || str_ends_with($path, '/register')
    ){
        require_once "{$dir}user.php";
        exit;
    }
    else if (str_contains($path, '/livro/')) {
        require_once "{$dir}/livro.php";
        exit;
    }
}

http_response_code(404);
echo json_encode(['error' => 'Rota principal não encontrada']);