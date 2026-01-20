<?php

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string {
        return dirname(__DIR__) . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('view_path')) {
    function view_path(string $path = ''): string {
        return base_path('views') . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('json_response')) {
    function json_response(array $data, int $status = 200): void {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}

if (!function_exists('validate_password_strength')) {
    function validate_password_strength(string $password): ?string {
        if (strlen($password) < 8) {
            return 'A senha deve ter pelo menos 8 caracteres.';
        } 
        else if (!preg_match('/[a-z]/', $password)) {
            return 'A senha deve ter pelo menos uma letra minúscula.';
        }
        else if (!preg_match('/[A-Z]/', $password)) {
            return 'A senha deve ter pelo menos uma letra maiúscula.';
        }
        else if (!preg_match('/[0-9]/', $password)) {
            return 'A senha deve ter pelo menos um número.';
        }
        else if (!preg_match('/[\W_]/', $password)) {
            return 'A senha deve ter pelo menos um caractere especial (ex: !@#$).';
        }
        
        return null; // Sem erros
    }
}

if (!function_exists('route_log')) {
    function route_log() {
        $date = new Datetime();
        $ip = $_SERVER['REMOTE_ADDR'];          // IP do cliente
        $method = $_SERVER['REQUEST_METHOD'];   // GET, POST, PUT, DELETE
        $route = $_SERVER['REQUEST_URI'];       // Ex: /api/login?id=1

        $message = "[{$date->format('Y-m-d H:i:s')}] {$ip}: {$method} - {$route}" . PHP_EOL;

        // 4. Define o caminho do arquivo (cria pasta 'logs' se não existir)
        $path = base_path('logs');
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        // Cria um arquivo por dia para não ficar gigante (ex: rotas-2025-12-16.log)
        $file = "{$path}/rotas-{$date->format('Y-m-d')}.log";

        // 5. Escreve no final do arquivo (FILE_APPEND)
        file_put_contents($file, $message, FILE_APPEND);
    }
}

if (!function_exists('slugificar')) {
    /**
     * Converte um texto em slug (sem acentos, espaços como traços)
     * Ex: "O Senhor dos Anéis" -> "o-senhor-dos-aneis"
     * 
     * @param string $texto Texto a ser slugificado
     * @return string Slug normalizado
     */
    function slugificar(string $texto): string {
        // Converte para minúsculas
        $texto = mb_strtolower($texto, 'UTF-8');
        
        // Remove acentos
        $texto = preg_replace('/[áàâãäå]/u', 'a', $texto);
        $texto = preg_replace('/[éèêë]/u', 'e', $texto);
        $texto = preg_replace('/[íìîï]/u', 'i', $texto);
        $texto = preg_replace('/[óòôõö]/u', 'o', $texto);
        $texto = preg_replace('/[úùûü]/u', 'u', $texto);
        $texto = preg_replace('/[ç]/u', 'c', $texto);
        
        // Remove caracteres especiais
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
        
        // Remove traços nas extremidades
        $texto = trim($texto, '-');
        
        return $texto;
    }
}
