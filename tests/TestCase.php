<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function refreshApplication(): void
    {
        parent::refreshApplication();

        // A suíte deve ser isolada mesmo se houver um config:cache local ativo.
        $this->app['config']->set([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Os testes exercitam as regras dos endpoints, não a proteção do navegador.
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }
}
