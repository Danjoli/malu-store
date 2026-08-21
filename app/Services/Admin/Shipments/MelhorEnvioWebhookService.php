<?php

namespace App\Services\Admin\Shipments;

use App\Models\Shipment;
use App\Services\Shipping\ShipmentStatusMapper;
use Illuminate\Support\Facades\Log;

class MelhorEnvioWebhookService
{
    public function __construct(private ShipmentStatusMapper $statusMapper) {}

    public function handleMelhorEnvio(array $data): void
    {
        Log::info('Webhook Melhor Envio', $data);

        if (! isset($data['id'])) {
            return;
        }

        $shipment = Shipment::where('shipment_id', $data['id'])->first();

        if (! $shipment) {
            return;
        }

        $shipment->update([
            'status' => $this->statusMapper->fromProvider($data['status'] ?? null) ?? $shipment->status,
            'tracking_code' => $data['tracking'] ?? $shipment->tracking_code,
            'label_url' => $data['label'] ?? $shipment->label_url,
            'last_update' => json_encode($data),
        ]);
    }

}
