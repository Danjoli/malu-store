<?php

use App\Http\Controllers\Public\WebhookController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| WEBHOOKS
|--------------------------------------------------------------------------
*/

Route::post('/webhooks/mercado-pago', [WebhookController::class, 'mercadoPago'])
    ->name('api.webhooks.mercado-pago');

Route::post('/webhooks/melhor-envio', [WebhookController::class, 'melhorEnvio'])
    ->name('api.webhooks.melhor-envio');
