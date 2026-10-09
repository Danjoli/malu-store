<?php

namespace Tests\Feature;

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_web_responses_include_baseline_security_headers(): void
    {
        $this->get(route('policy'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), geolocation=(), microphone=()')
            ->assertHeaderMissing('X-Powered-By');

        $policy = (string) $this->get(route('policy'))->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("default-src 'self'", $policy);
        $this->assertStringContainsString("object-src 'none'", $policy);
        $this->assertStringContainsString("frame-ancestors 'self'", $policy);
        $this->assertMatchesRegularExpression("/script-src 'self' 'nonce-[^']+' 'unsafe-eval'/", $policy);
    }

    public function test_https_responses_include_hsts_when_enabled(): void
    {
        config(['security.hsts_enabled' => true]);

        $request = Request::create('https://malu-store.test/policy');
        $response = app(SecurityHeaders::class)->handle($request, fn () => new Response);

        $this->assertSame(
            'max-age=31536000; includeSubDomains',
            $response->headers->get('Strict-Transport-Security'),
        );
    }
}
