<?php

use App\Http\Controllers\Public\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PAGAMENTOS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ESCOLHA DO MÉTODO DE PAGAMENTO
    |--------------------------------------------------------------------------
    */

    Route::get('/payment/{order}', [PaymentController::class, 'method'])
        ->name('payment.method');

    Route::post('/payment/{order}/process', [PaymentController::class, 'process'])
        ->middleware('throttle:payment')
        ->name('payment.process');

    /*
    |--------------------------------------------------------------------------
    | PIX
    |--------------------------------------------------------------------------
    */

    Route::post('/payment/pix/{order}', [PaymentController::class, 'pix'])
        ->middleware('throttle:payment')
        ->name('payment.pix');

    /*
    |--------------------------------------------------------------------------
    | BOLETO
    |--------------------------------------------------------------------------
    */

    Route::post('/payment/boleto/{order}', [PaymentController::class, 'boleto'])
        ->middleware('throttle:payment')
        ->name('payment.boleto');

    /*
    |--------------------------------------------------------------------------
    | CARTÃO
    |--------------------------------------------------------------------------
    */

    Route::post('/payment/card/{order}', [PaymentController::class, 'card'])
        ->middleware('throttle:payment')
        ->name('payment.card.process');

    /*
    |--------------------------------------------------------------------------
    | STATUS DO PAGAMENTO
    |--------------------------------------------------------------------------
    */

    Route::get('/payment/status/{order}', [PaymentController::class, 'status'])
        ->name('payment.status');

    /*
    |--------------------------------------------------------------------------
    | RESULTADO DO PAGAMENTO
    |--------------------------------------------------------------------------
    */

    Route::get('/payment-success/{order}', [PaymentController::class, 'success'])
        ->name('payment.success');

    Route::get('/payment-error/{order}', [PaymentController::class, 'error'])
        ->name('payment.error');
});
