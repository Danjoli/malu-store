<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\ShipmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| LOGIN ADMIN
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AdminAuthController::class, 'showLogin'])
        ->middleware('redirect.authenticated:admin')
        ->name('login');

    Route::post('/login', [AdminAuthController::class, 'login'])
        ->name('login.submit');

});

/*
|--------------------------------------------------------------------------
| ÁREA ADMINISTRATIVA
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('/logout', [AdminAuthController::class, 'logout'])
            ->name('logout');

        /*
        |--------------------------------------------------------------------------
        | SUPERADMIN
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.role:superadmin')->group(function () {

            Route::resource('admins', AdminController::class);
            Route::resource('clients', ClientController::class);
            Route::resource('categories', CategoryController::class);
            Route::resource('products', AdminProductController::class);
            Route::resource('orders', OrdersController::class);
            Route::resource('shipments', ShipmentController::class);

            Route::post('shipments/{id}/gerar-etiqueta', [ShipmentController::class, 'gerarEtiqueta'])
                ->name('shipments.gerar');

            // Atualizar status manual
            Route::post('shipments/{shipment}/atualizar-status', [ShipmentController::class, 'atualizarStatus'])
                ->name('shipments.atualizarStatus');
        });

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.role:admin,superadmin')->group(function () {

            Route::resource('clients', ClientController::class)->only(['index', 'show']);
            Route::resource('categories', CategoryController::class)->only(['index', 'show', 'update', 'destroy']);
            Route::resource('products', AdminProductController::class)->only(['index', 'show', 'update', 'destroy']);
            Route::resource('orders', OrdersController::class)->only(['index', 'show', 'update']);
            Route::resource('shipments', ShipmentController::class)->only(['index', 'show', 'update']);

        });

        /*
        |--------------------------------------------------------------------------
        | SUPORTE
        |--------------------------------------------------------------------------
        */

        Route::middleware('admin.role:suporte,admin,superadmin')->group(function () {

            Route::resource('orders', OrdersController::class)->only(['index', 'show']);
            Route::resource('shipments', ShipmentController::class)->only(['index', 'edit']);

        });

    });
