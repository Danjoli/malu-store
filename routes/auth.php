<?php

use App\Http\Controllers\Public\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTENTICAÇÃO DO CLIENTE
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware('redirect.authenticated:web')
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/esqueci-a-senha', [AuthController::class, 'showForgotPassword'])->middleware('redirect.authenticated:web')->name('password.request');
Route::post('/esqueci-a-senha', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
Route::get('/redefinir-senha/{token}', [AuthController::class, 'showResetPassword'])->middleware('redirect.authenticated:web')->name('password.reset');
Route::post('/redefinir-senha', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->middleware('redirect.authenticated:web')
    ->name('register');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');
