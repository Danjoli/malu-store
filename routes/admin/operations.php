<?php

use App\Enums\AdminRole;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\ShipmentController;
use Illuminate\Support\Facades\Route;

$superAdmin = AdminRole::SuperAdmin->value;
$admin = AdminRole::Admin->value;
$support = AdminRole::Support->value;
$operationsRoles = "admin.role:{$support},{$admin},{$superAdmin}";
$managementRoles = "admin.role:{$admin},{$superAdmin}";

Route::resource('orders', OrdersController::class)
    ->only(['index', 'show'])
    ->middleware($operationsRoles);

Route::resource('orders', OrdersController::class)
    ->only(['update'])
    ->middleware($managementRoles);

Route::resource('orders', OrdersController::class)
    ->only(['create', 'store', 'edit', 'destroy'])
    ->middleware("admin.role:{$superAdmin}");

Route::resource('shipments', ShipmentController::class)
    ->only(['index', 'show', 'edit'])
    ->middleware($operationsRoles);

Route::resource('shipments', ShipmentController::class)
    ->only(['update', 'destroy'])
    ->middleware($managementRoles);

Route::resource('shipments', ShipmentController::class)
    ->only(['create', 'store'])
    ->middleware("admin.role:{$superAdmin}");

Route::post('shipments/{id}/gerar-etiqueta', [ShipmentController::class, 'gerarEtiqueta'])
    ->middleware("admin.role:{$superAdmin}")
    ->name('shipments.gerar');

Route::post('shipments/{shipment}/atualizar-status', [ShipmentController::class, 'atualizarStatus'])
    ->middleware("admin.role:{$superAdmin}")
    ->name('shipments.atualizarStatus');
