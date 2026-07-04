<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
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
            'webhook/melhor-envio',
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

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->view('errors.404', [], 404);
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            return response()->view('errors.403', [], 403);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {

            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()->route('admin.login');
            }

            return redirect()->route('login');
        });

        // $exceptions->render(function (Throwable $e, Request $request) {
        //     return response()->view('errors.500', [], 500);
        // });

    })
    ->create();

