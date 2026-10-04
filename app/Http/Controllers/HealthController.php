<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = Cache::remember('health:readiness', now()->addSeconds(30), fn (): array => [
            'database' => $this->databaseIsReady(),
            'queue' => $this->queueIsReady(),
            'storage' => $this->storageIsReady(),
        ]);

        $ready = ! in_array(false, $checks, true);

        return response()->json([
            'status' => $ready ? 'ok' : 'unavailable',
            'checks' => $checks,
        ], $ready ? 200 : 503);
    }

    private function databaseIsReady(): bool
    {
        try {
            DB::select('select 1');

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function queueIsReady(): bool
    {
        $connection = config('queue.default');
        $queue = config("queue.connections.{$connection}");

        if (! is_array($queue) || ($queue['driver'] ?? null) !== 'database') {
            return true;
        }

        try {
            DB::connection($queue['connection'] ?? null)
                ->table($queue['table'] ?? 'jobs')
                ->limit(1)
                ->exists();

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function storageIsReady(): bool
    {
        $disk = Storage::disk();
        $path = 'healthchecks/'.Str::uuid().'.tmp';

        try {
            if (! $disk->put($path, 'ok')) {
                return false;
            }

            return $disk->delete($path);
        } catch (Throwable) {
            return false;
        }
    }
}
