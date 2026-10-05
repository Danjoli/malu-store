<?php

namespace Tests\Feature;

use App\Services\OperationalAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use Tests\TestCase;

class OperationalHealthCheckTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['queue.default' => 'database']);
    }

    public function test_it_succeeds_when_queues_are_healthy(): void
    {
        $this->mock(OperationalAlertService::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('critical');
        });

        $this->artisan('operations:check')
            ->expectsOutput('Operação saudável.')
            ->assertSuccessful();
    }

    public function test_it_alerts_for_failed_jobs_without_including_payloads(): void
    {
        DB::table('failed_jobs')->insert([
            'uuid' => 'failed-job-test',
            'connection' => 'database',
            'queue' => 'default',
            'payload' => '{"private":"must-not-be-alerted"}',
            'exception' => 'Synthetic failure',
            'failed_at' => now(),
        ]);

        $this->mock(OperationalAlertService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('critical')
                ->once()
                ->with('A Malu Store requer atenção operacional.', ['Jobs com falha' => 1]);
        });

        $this->artisan('operations:check')->assertFailed();
    }

    public function test_it_throttles_repeated_queue_delay_alerts(): void
    {
        config(['alerts.maximum_queue_delay_seconds' => 60]);
        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => '{}',
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => now()->subMinutes(5)->timestamp,
            'created_at' => now()->subMinutes(5)->timestamp,
        ]);

        $this->mock(OperationalAlertService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('critical')->once();
        });

        $this->artisan('operations:check')->assertFailed();
        $this->artisan('operations:check')->assertFailed();
    }
}
