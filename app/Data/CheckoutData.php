<?php

namespace App\Data;

final readonly class CheckoutData
{
    public function __construct(
        public ?int $addressId,
        public ?string $label,
        public string $recipientName,
        public string $phone,
        public string $cpf,
        public string $street,
        public string $number,
        public ?string $complement,
        public string $neighborhood,
        public string $city,
        public string $state,
        public string $cep,
        public float $shippingCost,
        public ?string $carrier,
        public ?string $service,
        public bool $isDefault,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            addressId: filled($data['address_id'] ?? null) ? (int) $data['address_id'] : null,
            label: filled($data['label'] ?? null) ? trim($data['label']) : null,
            recipientName: $data['recipient_name'], phone: $data['phone'],
            cpf: preg_replace('/\D/', '', $data['cpf'] ?? ''), street: $data['street'], number: $data['number'],
            complement: filled($data['complement'] ?? null) ? trim($data['complement']) : null,
            neighborhood: $data['neighborhood'], city: $data['city'], state: strtoupper(trim($data['state'])),
            cep: preg_replace('/\D/', '', $data['cep']), shippingCost: (float) ($data['shipping_cost'] ?? 0),
            carrier: $data['carrier'] ?? null, service: $data['service'] ?? null, isDefault: ! empty($data['is_default']),
        );
    }
}
