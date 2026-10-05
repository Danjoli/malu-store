<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Shared hosting disables proc_open, so scheduled work must run in-process.
Schedule::call(static function (): void {
    Artisan::call('operations:check');
})
    ->name('operations:check')
    ->everyFiveMinutes()
    ->withoutOverlapping(10);
