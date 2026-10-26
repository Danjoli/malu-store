<?php

namespace App\Jobs;

use App\Services\OperationalAlertService;
use App\UseCases\Shipping\GenerateShipmentLabel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateShipmentLabelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int> */
    public array $backoff = [5, 15, 30];

    public function __construct(public readonly int $shipmentId) {}

    public function handle(GenerateShipmentLabel $generateLabel): void
    {
        $generateLabel->execute($this->shipmentId);
    }

    public function failed(Throwable $exception): void
    {
        Log::critical('Geração de etiqueta falhou definitivamente.', [
            'shipment_id' => $this->shipmentId,
            'exception' => $exception::class,
        ]);

        app(OperationalAlertService::class)->critical(
            'Geração de etiqueta falhou após todas as tentativas.',
            [
                'Envio' => $this->shipmentId,
                'Tipo de erro' => $exception::class,
            ],
        );
    }
}
