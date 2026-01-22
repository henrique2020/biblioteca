<?php
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
route_log();

// 1. Define o cabeçalho padrão (pode ser sobrescrito nas views)
header('Content-Type: application/json');

// 2. Roteamento de Frontend
if ($path === '/' || $path === '/login') {
    header('Content-Type: text/html; charset=UTF-8');
    require_once view_path('login.php');
    exit;
}

if ($path === '/home') {
    header('Content-Type: text/html; charset=UTF-8');
    require_once view_path('home.php');
    exit;
}

if ($path === '/perfil') {
    header('Content-Type: text/html; charset=UTF-8');
    require_once view_path('usuario/perfil.php');
    exit;
}

if ($path === '/cadastre-se') {
    header('Content-Type: text/html; charset=UTF-8');
    require_once view_path('usuario/cadastro.php');
    exit;
}

if ($path === '/catalogo') {
    header('Content-Type: text/html; charset=UTF-8');
    require_once view_path('catalogo.php');
    exit;
}

// Rota dinâmica: /livro/{slug}
if (str_starts_with($path, '/livro/')) {
    header('Content-Type: text/html; charset=UTF-8');
    
    // Extrai o slug da URL
    $slug = str_replace('/livro/', '', $path);
    
    $slug = explode('?', $slug)[0];
    $slug = trim($slug, '/');
    
    // Se não tiver um slug, redireciona para home
    if (empty($slug)) {
        header('Location: /home');
        exit;
    }
    
    require_once view_path('livro.php');
    exit;
}

// 3. Roteamento de API
// Se a rota começa com /api/user...
if (
    str_starts_with($path, '/api/user')
    || str_starts_with($path, '/api/login')
    || str_starts_with($path, '/api/register')
) {
    require_once __DIR__ . '/user.php';
    exit;
}

if (str_starts_with($path, '/api/livro')) {
    require_once __DIR__ . '/livro.php';
    exit;
}

// 5. Se nada for encontrado
http_response_code(404);
echo json_encode(['error' => 'Rota principal não encontrada']);