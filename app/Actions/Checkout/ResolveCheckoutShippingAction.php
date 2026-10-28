<?php

namespace App\Actions\Checkout;

use App\Actions\Shipping\CalculateShippingOptionsAction;
use Illuminate\Validation\ValidationException;

class ResolveCheckoutShippingAction
{
    public function __construct(private CalculateShippingOptionsAction $calculateShipping) {}

    public function execute(array $input): array
    {
        $serviceId = (string) ($input['service'] ?? '');
        $options = $this->calculateShipping->execute((string) $input['cep']);
        $selected = $options->first(
            fn (array $option): bool => (string) ($option['id'] ?? '') === $serviceId
                && isset($option['price'])
                && (float) $option['price'] > 0
        );

        if (! is_array($selected)) {
            throw ValidationException::withMessages([
                'service' => 'A opção de entrega expirou ou não é mais válida. Calcule o frete novamente.',
            ]);
        }

        return [
            ...$input,
            'shipping_cost' => (float) $selected['price'],
            'carrier' => (string) ($selected['name'] ?? 'Melhor Envio'),
            'service' => $serviceId,
        ];
    }
}
