<?php

namespace App\Services\Admin\Shipments;

use App\Models\Shipment;
use App\Services\Shipping\ShipmentStatusMapper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class MelhorEnvioWebhookService
{
    public function __construct(private ShipmentStatusMapper $statusMapper) {}

    public function handleMelhorEnvio(array $data): void
    {
        Log::info('Webhook Melhor Envio recebido.', [
            'shipment_id' => $data['id'] ?? null,
            'status' => $data['status'] ?? null,
        ]);

        if (! isset($data['id'])) {
            return;
        }

        $shipment = Shipment::where('shipment_id', $data['id'])->first();

        if (! $shipment) {
            return;
        }

        $shipment->update([
            'status' => $this->statusMapper->fromProvider($data['status'] ?? null)?->value ?? $shipment->status,
            'tracking_code' => $data['tracking'] ?? $shipment->tracking_code,
            'label_url' => $data['label'] ?? $shipment->label_url,
            'last_update' => $this->snapshot($data),
        ]);
    }

    /** @param array<string, mixed> $data */
    private function snapshot(array $data): ?string
    {
        return json_encode(Arr::only($data, [
            'id',
            'status',
            'tracking',
            'label',
            'updated_at',
        ])) ?: null;
    }
}
