<?php

namespace Tests\Feature;

use App\Services\Shipping\ShipmentStatusMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ShipmentStatusMapperTest extends TestCase
{
    #[DataProvider('statuses')]
    public function test_it_maps_provider_statuses(?string $providerStatus, ?string $expectedStatus): void
    {
        $this->assertSame($expectedStatus, app(ShipmentStatusMapper::class)->fromProvider($providerStatus));
    }

    public static function statuses(): array
    {
        return [
            'posted shipment' => ['posted', 'in_transit'],
            'delivered shipment' => ['delivered', 'delivered'],
            'unknown shipment' => ['unknown', null],
        ];
    }
}
