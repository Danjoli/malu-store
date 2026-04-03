<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Verifica modo de manutenção
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload do Composer (CORRIGIDO)
require __DIR__.'/../vendor/autoload.php';

// Bootstrap do Laravel (CORRIGIDO)
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Processa a requisição
$app->handleRequest(Request::capture());
