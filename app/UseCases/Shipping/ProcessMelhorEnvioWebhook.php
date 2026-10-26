<?php

namespace App\UseCases\Shipping;

use App\Data\Webhooks\MelhorEnvioWebhookData;
use App\Models\Shipment;
use App\Services\Shipping\ShipmentStatusMapper;
use Illuminate\Support\Facades\Log;

class ProcessMelhorEnvioWebhook
{
    public function __construct(private ShipmentStatusMapper $statusMapper) {}

    public function execute(array $payload): void
    {
        $data = MelhorEnvioWebhookData::fromArray($payload);

        Log::info('Webhook Melhor Envio recebido.', [
            'shipment_id' => $data->id,
            'status' => $data->status,
        ]);

        if (! $data->id) {
            return;
        }

        $shipment = Shipment::where('shipment_id', $data->id)->first();

        if (! $shipment) {
            return;
        }

        $shipment->update([
            'status' => $this->statusMapper->fromProvider($data->status)?->value ?? $shipment->status,
            'tracking_code' => $data->tracking ?? $shipment->tracking_code,
            'label_url' => $data->label ?? $shipment->label_url,
            'last_update' => json_encode($data->snapshot()) ?: null,
        ]);
    }
}
