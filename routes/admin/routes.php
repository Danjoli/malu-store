<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/auth.php';

    Route::middleware(['auth:admin', 'admin.active'])->group(function () {
        require __DIR__.'/dashboard.php';
        require __DIR__.'/management.php';
        require __DIR__.'/operations.php';
    });
});
