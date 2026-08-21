<?php

use App\Http\Controllers\Public\AddressController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\CheckoutController;
use App\Http\Controllers\Public\FavoriteController;
use App\Http\Controllers\Public\FreteController;
use App\Http\Controllers\Public\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ÁREA DO CLIENTE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PERFIL
    |--------------------------------------------------------------------------
    */

    Route::prefix('perfil')->group(function () {

        Route::get('/', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::put('/', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::put('/senha', [ProfileController::class, 'updatePassword'])
            ->name('profile.password.update');

        /*
        |--------------------------------------------------------------------------
        | PEDIDOS
        |--------------------------------------------------------------------------
        */

        Route::get('/pedidos', [ProfileController::class, 'orders'])
            ->name('profile.orders');

        Route::get('/pedidos/{id}', [ProfileController::class, 'orderShow'])
            ->name('profile.orders.show');

        /*
        |--------------------------------------------------------------------------
        | ENDEREÇOS
        |--------------------------------------------------------------------------
        */

        Route::post('/endereco/{id}/default', [AddressController::class, 'setDefault'])
            ->name('profile.address.default');

        Route::delete('/endereco/{id}', [AddressController::class, 'destroy'])
            ->name('profile.address.delete');
    });

    /*
    |--------------------------------------------------------------------------
    | CARRINHO
    |--------------------------------------------------------------------------
    */

    Route::prefix('cart')->group(function () {

        Route::get('/', [CartController::class, 'index'])
            ->name('public.cart.index');

        Route::post('/add', [CartController::class, 'add'])
            ->name('cart.add');

        Route::put('/{id}', [CartController::class, 'update'])
            ->name('cart.update');

        Route::delete('/{id}', [CartController::class, 'remove'])
            ->name('cart.remove');
    });

    Route::get('/favoritos', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favoritos/{product}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    /*
    |--------------------------------------------------------------------------
    | ENDEREÇOS
    |--------------------------------------------------------------------------
    */

    Route::post('/addresses', [AddressController::class, 'store'])
        ->name('addresses.store');

    Route::put('/addresses/{id}', [AddressController::class, 'update'])
        ->name('addresses.update');

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT
    |--------------------------------------------------------------------------
    */

    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout');

    Route::post('/checkout/process', [CheckoutController::class, 'processOrder'])
        ->name('checkout.process');

});

// Também é usado como estimativa antes de o cliente entrar no checkout.
Route::post('/frete/calcular', [FreteController::class, 'calcular'])
    ->middleware('throttle:10,1')
    ->name('frete.calcular');
