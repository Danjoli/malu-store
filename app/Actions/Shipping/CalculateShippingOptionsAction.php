<?php

namespace App\Actions\Shipping;

use App\Services\Shipping\MelhorEnvioService;
use Illuminate\Support\Collection;

class CalculateShippingOptionsAction
{
    public function __construct(private MelhorEnvioService $melhorEnvio) {}

    public function execute(string $destinationZip): Collection
    {
        $result = $this->melhorEnvio->calcularFrete([
            'from' => ['postal_code' => config('shipping.origin_zip')],
            'to' => ['postal_code' => $destinationZip],
            'products' => [[
                'id' => '1',
                'width' => 15,
                'height' => 10,
                'length' => 20,
                'weight' => 1,
                'insurance_value' => 100,
                'quantity' => 1,
            ]],
        ]);

        return collect($result)
            ->filter(fn (array $option) => isset($option['price']) && $option['price'] > 0)
            ->values();
    }
}
