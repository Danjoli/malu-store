<?php

use App\Http\Controllers\Webhooks\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WEBHOOKS DE INTEGRAÇÕES EXTERNAS
|--------------------------------------------------------------------------
*/

Route::post('/webhooks/asaas', [WebhookController::class, 'asaas'])
    ->name('api.webhooks.asaas');

Route::post('/webhooks/melhor-envio', [WebhookController::class, 'melhorEnvio'])
    ->name('api.webhooks.melhor-envio');
