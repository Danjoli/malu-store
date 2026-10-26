<?php

namespace App\Data\Webhooks;

final readonly class AsaasWebhookData
{
    /**
     * @param  array<string, mixed>  $payment
     * @param  array<string, mixed>  $checkout
     */
    private function __construct(
        public ?string $event,
        public array $payment,
        public array $checkout,
    ) {}

    /** @param array<string, mixed> $payload */
    public static function fromArray(array $payload): self
    {
        return new self(
            event: is_string($payload['event'] ?? null) ? $payload['event'] : null,
            payment: is_array($payload['payment'] ?? null) ? $payload['payment'] : [],
            checkout: is_array($payload['checkout'] ?? null) ? $payload['checkout'] : [],
        );
    }

    /** @return array{payment: array<string, mixed>} */
    public function paymentPayload(): array
    {
        return ['payment' => $this->payment];
    }
}
