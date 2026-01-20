<?php
date_default_timezone_set('America/Sao_Paulo');

// Carrega o autoloader do Composer
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Carrega variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(base_path());
$dotenv->load();

// Carrega as rotas
require_once base_path('routes/index.php');