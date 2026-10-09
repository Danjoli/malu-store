<?php

namespace App\Services\Security;

class WebhookSignatureValidator
{
    public function asaas(?string $receivedToken): bool
    {
        $expectedToken = config('services.asaas.webhook_token');

        return is_string($expectedToken)
            && $expectedToken !== ''
            && is_string($receivedToken)
            && hash_equals($expectedToken, $receivedToken);
    }

    public function melhorEnvio(string $payload, ?string $receivedSignature): bool
    {
        $secret = config('services.melhor_envio.webhook_secret');

        if (! is_string($secret) || $secret === '' || ! is_string($receivedSignature)) {
            return false;
        }

        $signature = preg_replace('/^sha256=/i', '', trim($receivedSignature));
        $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $secret, true));

        return hash_equals($expectedSignature, $signature);
    }
}
