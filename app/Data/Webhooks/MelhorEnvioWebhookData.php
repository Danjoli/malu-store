<?php

namespace App\Data\Webhooks;

final readonly class MelhorEnvioWebhookData
{
    private function __construct(
        public ?string $id,
        public ?string $status,
        public ?string $tracking,
        public ?string $label,
        public ?string $updatedAt,
    ) {}

    /** @param array<string, mixed> $payload */
    public static function fromArray(array $payload): self
    {
        return new self(
            id: self::stringOrNull($payload['id'] ?? null),
            status: self::stringOrNull($payload['status'] ?? null),
            tracking: self::stringOrNull($payload['tracking'] ?? null),
            label: self::stringOrNull($payload['label'] ?? null),
            updatedAt: self::stringOrNull($payload['updated_at'] ?? null),
        );
    }

    /** @return array<string, string|null> */
    public function snapshot(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'tracking' => $this->tracking,
            'label' => $this->label,
            'updated_at' => $this->updatedAt,
        ];
    }

    private static function stringOrNull(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }
}
