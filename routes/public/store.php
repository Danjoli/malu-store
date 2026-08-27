<?php

use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PublicProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| VITRINE E PÁGINAS INSTITUCIONAIS
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/produtos', [CatalogController::class, 'index'])
    ->name('catalog.index');

Route::view('/policy', 'public.legal.policy')
    ->name('policy');

Route::view('/terms', 'public.legal.terms')
    ->name('terms');

Route::view('/privacy', 'public.legal.privacy')
    ->name('privacy');

Route::get('/produtos/{product}', [PublicProductController::class, 'show'])
    ->name('product.show');

Route::get('/product/{id}', [PublicProductController::class, 'legacyShow'])
    ->whereNumber('id')
    ->name('product.legacy-show');
