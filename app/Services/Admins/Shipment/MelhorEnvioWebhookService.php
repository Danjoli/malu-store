<?php

namespace App\Services\Shipment;

use App\Models\Shipment;
use Illuminate\Support\Facades\Log;

class MelhorEnvioWebhookService
{
    public function handleMelhorEnvio(array $data): void
    {
        Log::info('Webhook Melhor Envio', $data);

        if (!isset($data['id'])) {
            return;
        }

        $shipment = Shipment::where('shipment_id', $data['id'])->first();

        if (!$shipment) {
            return;
        }

        $shipment->update([
            'status' => $this->mapStatus($data['status'] ?? null) ?? $shipment->status,
            'tracking_code' => $data['tracking'] ?? $shipment->tracking_code,
            'label_url' => $data['label'] ?? $shipment->label_url,
            'last_update' => json_encode($data),
        ]);
    }

    private function mapStatus(?string $status): ?string
    {
        return [
            'created' => 'pending',
            'released' => 'waiting_post',
            'generated' => 'waiting_post',
            'posted' => 'in_transit',
            'in_transit' => 'in_transit',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled',
        ][$status] ?? null;
    }
}
