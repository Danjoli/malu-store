<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/admin/auth.php';

    Route::middleware('auth:admin')->group(function () {
        require __DIR__.'/admin/dashboard.php';
        require __DIR__.'/admin/management.php';
        require __DIR__.'/admin/operations.php';
    });
});
