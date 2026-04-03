<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | LIBERAR CSRF PARA WEBHOOK
        |--------------------------------------------------------------------------
        */
        $middleware->validateCsrfTokens(except: [
            'webhook/mercadopago',
        ]);

        /*
        |--------------------------------------------------------------------------
        | REDIRECIONAMENTO DE NÃO AUTENTICADOS
        |--------------------------------------------------------------------------
        */
        $middleware->redirectGuestsTo(function (Request $request) {

            // se tentar acessar /admin
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            // login normal de cliente
            return route('login');
        });

        /*
        |--------------------------------------------------------------------------
        | MIDDLEWARES CUSTOM
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            'admin.role' => \App\Http\Middleware\AdminRole::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

