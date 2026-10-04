<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;

class RequestIdTest extends TestCase
{
    public function test_response_contains_a_generated_request_id(): void
    {
        $response = $this->get('/health');

        $response->assertOk();
        $this->assertTrue(Str::isUuid((string) $response->headers->get('X-Request-ID')));
    }

    public function test_valid_client_request_id_is_preserved(): void
    {
        $requestId = (string) Str::uuid();

        $this->get('/health', ['X-Request-ID' => $requestId])
            ->assertOk()
            ->assertHeader('X-Request-ID', $requestId);
    }

    public function test_untrusted_request_id_is_replaced(): void
    {
        $response = $this->get('/health', ['X-Request-ID' => 'invalid-value']);

        $response->assertOk();
        $this->assertNotSame('invalid value', $response->headers->get('X-Request-ID'));
        $this->assertTrue(Str::isUuid((string) $response->headers->get('X-Request-ID')));
    }
}
