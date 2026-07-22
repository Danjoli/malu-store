<?php

use App\Http\Controllers\Webhooks\WebhookController;
use Illuminate\Support\Facades\Route;
use App\Models\Order;


/*
|--------------------------------------------------------------------------
| WEBHOOKS
|--------------------------------------------------------------------------
*/

Route::post('/webhooks/asaas', [WebhookController::class, 'asaas'])
    ->name('api.webhooks.asaas');

Route::post('/webhooks/melhor-envio', [WebhookController::class, 'melhorEnvio'])
    ->name('api.webhooks.melhor-envio');

Route::get('/teste/order/{id}', function ($id) {
    return Order::findOrFail($id);
});
