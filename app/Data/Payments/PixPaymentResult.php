<?php

namespace App\Data\Payments;

final readonly class PixPaymentResult
{
    /** @param array<string, mixed> $payment */
    public function __construct(
        public array $payment,
        public string $qrCodeBase64,
        public string $qrCode,
    ) {}
}
