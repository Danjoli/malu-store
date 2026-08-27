<?php

namespace App\Services\Shipping;

use App\Enums\ShipmentStatus;

class ShipmentStatusMapper
{
    public function fromProvider(?string $status): ?ShipmentStatus
    {
        return [
            'created' => ShipmentStatus::Pending,
            'released' => ShipmentStatus::WaitingPost,
            'generated' => ShipmentStatus::WaitingPost,
            'posted' => ShipmentStatus::InTransit,
            'in_transit' => ShipmentStatus::InTransit,
            'delivered' => ShipmentStatus::Delivered,
            'cancelled' => ShipmentStatus::Cancelled,
        ][$status] ?? null;
    }
}
