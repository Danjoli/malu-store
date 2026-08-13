<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\PublicProductController;

/*
|--------------------------------------------------------------------------
| LOJA PÚBLICA
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/produtos', [CatalogController::class, 'index'])
    ->name('catalog.index');

Route::view('/policy', 'public.pages.policy')
    ->name('policy');

Route::view('/terms', 'public.pages.terms')
    ->name('terms');

Route::view('/privacy', 'public.pages.privacy')
    ->name('privacy');

Route::get('/product/{id}', [PublicProductController::class, 'show'])
    ->name('product.show');

/*
|--------------------------------------------------------------------------
| ARQUIVOS DE ROTAS
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
require __DIR__.'/customer.php';
require __DIR__.'/payment.php';
require __DIR__.'/admin.php';
