<?php

/*
|--------------------------------------------------------------------------
| PONTO DE ENTRADA DAS ROTAS WEB
|--------------------------------------------------------------------------
|
| As rotas são agrupadas por área de responsabilidade. Este arquivo fica
| deliberadamente curto para tornar a estrutura fácil de localizar.
|
*/

use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class)
    ->middleware('throttle:30,1')
    ->name('health');

require __DIR__.'/public/store.php';
require __DIR__.'/public/auth.php';
require __DIR__.'/public/customer.php';
require __DIR__.'/public/payment.php';
require __DIR__.'/admin/routes.php';
