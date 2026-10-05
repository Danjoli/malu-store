<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class SchedulerCompatibilityTest extends TestCase
{
    public function test_scheduled_operations_run_in_process_without_proc_open(): void
    {
        $events = app(Schedule::class)->events();

        $operationsCheck = collect($events)->first(
            fn ($event): bool => $event->description === 'operations:check'
        );

        $this->assertInstanceOf(CallbackEvent::class, $operationsCheck);
    }
}
