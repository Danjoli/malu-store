<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\ShipmentController;

/*
|--------------------------------------------------------------------------
| WEBHOOK (FORA DO AUTH - ESSENCIAL)
|--------------------------------------------------------------------------
*/

Route::post('/webhook/melhor-envio', [ShipmentController::class, 'webhook'])
    ->name('webhook.melhor-envio');
