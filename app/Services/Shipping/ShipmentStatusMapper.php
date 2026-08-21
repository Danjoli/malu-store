<?php

namespace App\Services\Shipping;

class ShipmentStatusMapper
{
    public function fromProvider(?string $status): ?string
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
