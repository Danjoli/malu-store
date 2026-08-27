<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExceptionRenderingTest extends TestCase
{
    public function test_missing_html_page_uses_the_store_error_view(): void
    {
        $this->get('/pagina-inexistente')
            ->assertNotFound()
            ->assertViewIs('errors.404');
    }

    public function test_missing_api_route_keeps_the_default_json_response(): void
    {
        $this->getJson('/api/recurso-inexistente')
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    }
}
