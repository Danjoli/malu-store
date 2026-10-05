<?php

namespace Tests\Feature;

use App\Jobs\QueueHealthCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class QueueHealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_queue_health_check_records_success_without_sensitive_data(): void
    {
        $key = 'queue-healthcheck:test';

        Cache::forget($key);

        (new QueueHealthCheck($key))->handle();

        $this->assertSame('processed', Cache::pull($key));
    }
}
