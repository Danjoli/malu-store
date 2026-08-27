<?php

namespace App\Exceptions\Domain;

class PaymentGatewayException extends PaymentException
{
    public function __construct(
        string $message,
        public readonly bool $isOperational = true,
    ) {
        parent::__construct($message);
    }
}
