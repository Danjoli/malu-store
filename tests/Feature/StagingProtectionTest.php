<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StagingProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_staging_rejects_anonymous_visitors_and_prevents_indexing(): void
    {
        app()->detectEnvironment(fn (): string => 'staging');
        config([
            'staging.basic_auth.username' => 'reviewer',
            'staging.basic_auth.password' => 'secret-password',
        ]);

        $this->get('/')
            ->assertUnauthorized()
            ->assertHeader('WWW-Authenticate')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function test_staging_accepts_valid_basic_authentication(): void
    {
        app()->detectEnvironment(fn (): string => 'staging');
        config([
            'staging.basic_auth.username' => 'reviewer',
            'staging.basic_auth.password' => 'secret-password',
        ]);

        $this->withBasicAuth('reviewer', 'secret-password')
            ->get('/')
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function test_production_is_not_affected_by_staging_authentication(): void
    {
        app()->detectEnvironment(fn (): string => 'production');
        config([
            'staging.basic_auth.username' => 'reviewer',
            'staging.basic_auth.password' => 'secret-password',
        ]);

        $this->get('/')->assertOk();
    }
}
