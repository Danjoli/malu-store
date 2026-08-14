<?php

use App\Http\Controllers\Public\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTENTICAÇÃO DO CLIENTE
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware('redirect.authenticated')
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])
    ->middleware('redirect.authenticated')
    ->name('register');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');
