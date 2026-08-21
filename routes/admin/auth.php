<?php

use App\Http\Controllers\Admin\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AdminAuthController::class, 'showLogin'])
    ->middleware('redirect.authenticated:admin')
    ->name('login');

Route::post('/login', [AdminAuthController::class, 'login'])
    ->name('login.submit');

Route::get('/esqueci-a-senha', [AdminAuthController::class, 'showForgotPassword'])
    ->middleware('redirect.authenticated:admin')
    ->name('password.request');

Route::post('/esqueci-a-senha', [AdminAuthController::class, 'sendResetLink'])
    ->middleware('throttle:5,1')
    ->name('password.email');

Route::get('/redefinir-senha/{token}', [AdminAuthController::class, 'showResetPassword'])
    ->middleware('redirect.authenticated:admin')
    ->name('password.reset');

Route::post('/redefinir-senha', [AdminAuthController::class, 'resetPassword'])
    ->middleware('throttle:5,1')
    ->name('password.update');
