<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Predis\Retry\Strategy\ExponentialBackoff;
use Tests\TestCase;

class RedisCacheFailoverTest extends TestCase
{
    use RefreshDatabase;

    public function test_cache_uses_database_when_redis_is_temporarily_unavailable(): void
    {
        config([
            'database.redis.client' => 'predis',
            'database.redis.cache' => [
                'url' => null,
                'host' => '127.0.0.1',
                'password' => null,
                'port' => 1,
                'database' => 1,
                'timeout' => 0.05,
                'read_write_timeout' => 0.05,
                'max_retries' => 0,
                'retry' => [ExponentialBackoff::class => [1000, 1000, true]],
            ],
            'cache.stores.failover.stores' => ['redis', 'database', 'array'],
        ]);

        Cache::purge('redis');
        Cache::purge('database');
        Cache::purge('failover');

        $cache = Cache::store('failover');

        $this->assertTrue($cache->put('redis-failover-test', 'ok', 60));
        $this->assertSame('ok', $cache->get('redis-failover-test'));
        $this->assertDatabaseHas('cache', ['key' => config('cache.prefix').'redis-failover-test']);
    }
}
