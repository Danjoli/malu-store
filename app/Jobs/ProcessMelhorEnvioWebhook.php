<?php

namespace App\Jobs;

use App\Services\Admin\Shipments\MelhorEnvioWebhookService;
use App\Services\OperationalAlertService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessMelhorEnvioWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(public readonly array $payload)
    {
        $this->onQueue('critical');
    }

    public function handle(MelhorEnvioWebhookService $webhook): void
    {
        $webhook->handleMelhorEnvio($this->payload);
    }

    public function failed(Throwable $exception): void
    {
        Log::critical('Webhook Melhor Envio falhou definitivamente.', [
            'shipment_id' => $this->payload['id'] ?? null,
            'exception' => $exception::class,
        ]);

        app(OperationalAlertService::class)->critical(
            'Webhook do Melhor Envio falhou após todas as tentativas.',
            [
                'Envio' => $this->payload['id'] ?? null,
                'Tipo de erro' => $exception::class,
            ],
        );
    }
}
