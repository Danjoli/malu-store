<?php

namespace App\Data\Payments;

final readonly class BoletoPaymentResult
{
    /** @param array<string, mixed> $payment */
    public function __construct(
        public array $payment,
        public ?string $boletoUrl,
        public ?string $invoiceUrl,
        public ?string $expiresAt,
    ) {}
}
