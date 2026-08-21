<?php

use App\Enums\AdminRole;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Catalog\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClientController;
use Illuminate\Support\Facades\Route;

$superAdmin = AdminRole::SuperAdmin->value;
$admin = AdminRole::Admin->value;
$managementRoles = "admin.role:{$admin},{$superAdmin}";

Route::resource('admins', AdminController::class)
    ->middleware("admin.role:{$superAdmin}");

Route::resource('clients', ClientController::class)
    ->only(['index', 'show'])
    ->middleware($managementRoles);

Route::resource('clients', ClientController::class)
    ->except(['index', 'show'])
    ->middleware("admin.role:{$superAdmin}");

Route::resource('categories', CategoryController::class)
    ->only(['index', 'show', 'update', 'destroy'])
    ->middleware($managementRoles);

Route::resource('categories', CategoryController::class)
    ->only(['create', 'store', 'edit'])
    ->middleware("admin.role:{$superAdmin}");

Route::resource('products', ProductController::class)
    ->only(['index', 'show', 'update', 'destroy'])
    ->middleware($managementRoles);

Route::resource('products', ProductController::class)
    ->only(['create', 'store', 'edit'])
    ->middleware("admin.role:{$superAdmin}");
