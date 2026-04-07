<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URIs que devem ser excluídas da verificação CSRF
     */
    protected $except = [
        '/webhook/melhor-envio',
    ];
}
