<?php

namespace App\Jobs;

use App\Services\OperationalAlertService;
use App\Services\Public\Payment\AsaasWebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessAsaasWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(public array $payload) {}

    public function handle(AsaasWebhookService $asaasWebhook): void
    {
        $asaasWebhook->handleAsaas($this->payload);
    }

    public function failed(Throwable $exception): void
    {
        // Executado apenas depois que todas as tentativas da fila falham.
        Log::critical('Webhook Asaas falhou definitivamente após as tentativas.', [
            'event' => $this->payload['event'] ?? null,
            'payment_id' => $this->payload['payment']['id'] ?? null,
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
        ]);

        app(OperationalAlertService::class)->critical(
            'Webhook do Asaas falhou após todas as tentativas.',
            [
                'Evento' => $this->payload['event'] ?? null,
                'Referência do pagamento' => $this->payload['payment']['id'] ?? null,
                'Tipo de erro' => $exception::class,
            ],
        );
    }
}
