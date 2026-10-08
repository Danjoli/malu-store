<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanonicalUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_use_the_configured_canonical_domain_without_query_parameters(): void
    {
        config()->set('app.url', 'https://malu-store.com');

        $this->get('/produtos?search=vestido')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="https://malu-store.com/produtos">', false);
    }
}
