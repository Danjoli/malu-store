<?php

namespace App\Console\Commands;

use App\Services\OperationalAlertService;
use Illuminate\Console\Command;
use Illuminate\Queue\DatabaseQueue;
use Illuminate\Queue\RedisQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Throwable;

class CheckOperationalHealth extends Command
{
    protected $signature = 'operations:check';

    protected $description = 'Verifica jobs falhos e filas paradas sem expor dados dos clientes';

    public function handle(OperationalAlertService $alerts): int
    {
        $failedJobs = DB::table('failed_jobs')->count();
        [$pendingJobs, $queueDelaySeconds, $queueAvailable] = $this->queueMetrics();
        $maximumQueueDelay = (int) config('alerts.maximum_queue_delay_seconds', 600);
        $maximumPendingJobs = (int) config('alerts.maximum_pending_jobs', 100);

        $problems = [];
        if ($failedJobs > 0) {
            $problems['Jobs com falha'] = $failedJobs;
        }

        if ($queueDelaySeconds > $maximumQueueDelay) {
            $problems['Atraso da fila em segundos'] = $queueDelaySeconds;
        }

        if ($pendingJobs > $maximumPendingJobs) {
            $problems['Jobs aguardando processamento'] = $pendingJobs;
        }

        if (! $queueAvailable) {
            $problems['Conexão com a fila'] = 'indisponível';
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

    /**
     * @return array{0: int, 1: int, 2: bool}
     */
    private function queueMetrics(): array
    {
        try {
            $connection = Queue::connection();
            $queues = config('alerts.monitored_queues', ['critical', 'default']);
            $queues = is_array($queues) ? $queues : ['critical', 'default'];

            if ($connection instanceof DatabaseQueue) {
                $oldestPendingJob = DB::connection(config('queue.connections.database.connection'))
                    ->table(config('queue.connections.database.table', 'jobs'))
                    ->whereNull('reserved_at')
                    ->min('available_at');

                return [
                    $connection->size(),
                    is_numeric($oldestPendingJob) ? max(0, now()->timestamp - (int) $oldestPendingJob) : 0,
                    true,
                ];
            }

            if ($connection instanceof RedisQueue) {
                $redis = $connection->getRedis()
                    ->connection(config('queue.connections.redis.connection', 'default'));
                $redis->command('ping');

                $pendingJobs = 0;
                $oldestCreatedAt = null;

                foreach ($queues as $queue) {
                    $pendingJobs += $connection->size($queue);

                    $payload = $redis->command('lindex', ["queues:{$queue}", 0]);
                    $createdAt = is_string($payload) ? data_get(json_decode($payload, true), 'createdAt') : null;

                    if (is_numeric($createdAt) && ($oldestCreatedAt === null || (int) $createdAt < $oldestCreatedAt)) {
                        $oldestCreatedAt = (int) $createdAt;
                    }
                }

                return [
                    $pendingJobs,
                    $oldestCreatedAt !== null ? max(0, now()->timestamp - $oldestCreatedAt) : 0,
                    true,
                ];
            }

            return [0, 0, true];
        } catch (Throwable) {
            return [0, 0, false];
        }
    }
}
