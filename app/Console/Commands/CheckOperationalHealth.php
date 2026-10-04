<?php

namespace App\Console\Commands;

use App\Services\OperationalAlertService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CheckOperationalHealth extends Command
{
    protected $signature = 'operations:check';

    protected $description = 'Verifica jobs falhos e filas paradas sem expor dados dos clientes';

    public function handle(OperationalAlertService $alerts): int
    {
        $failedJobs = DB::table('failed_jobs')->count();
        $oldestPendingJob = DB::table('jobs')
            ->whereNull('reserved_at')
            ->min('available_at');

        $queueDelaySeconds = is_numeric($oldestPendingJob)
            ? max(0, now()->timestamp - (int) $oldestPendingJob)
            : 0;
        $maximumQueueDelay = (int) config('alerts.maximum_queue_delay_seconds', 600);

        $problems = [];
        if ($failedJobs > 0) {
            $problems['Jobs com falha'] = $failedJobs;
        }

        if ($queueDelaySeconds > $maximumQueueDelay) {
            $problems['Atraso da fila em segundos'] = $queueDelaySeconds;
        }

        if ($problems === []) {
            Cache::forget('operations:health-alert');
            $this->info('Operação saudável.');

            return self::SUCCESS;
        }

        $throttleMinutes = (int) config('alerts.throttle_minutes', 60);
        if (Cache::add('operations:health-alert', true, now()->addMinutes($throttleMinutes))) {
            $alerts->critical('A Malu Store requer atenção operacional.', $problems);
        }

        foreach ($problems as $label => $value) {
            $this->error("{$label}: {$value}");
        }

        return self::FAILURE;
    }
}
