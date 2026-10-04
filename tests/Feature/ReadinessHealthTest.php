<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReadinessHealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_readiness_endpoint_checks_required_dependencies(): void
    {
        Storage::fake('public');
        Cache::forget('health:readiness');

        $this->getJson('/health')
            ->assertOk()
            ->assertExactJson([
                'status' => 'ok',
                'checks' => [
                    'database' => true,
                    'queue' => true,
                    'storage' => true,
                ],
            ]);

        $this->assertSame([], Storage::disk('public')->allFiles('healthchecks'));
    }
}
