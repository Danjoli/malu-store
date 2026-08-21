<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Pix = 'pix';
    case Card = 'card';
    case Boleto = 'boleto';

    public function label(): string
    {
        return match ($this) {
            self::Pix => 'Pix',
            self::Card => 'Cartão de crédito',
            self::Boleto => 'Boleto bancário',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Pix => 'Confirmação rápida e segura.',
            self::Card => 'Preencha os dados do cartão abaixo.',
            self::Boleto => 'O boleto será gerado após a confirmação.',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pix => 'PIX',
            self::Card => 'CARD',
            self::Boleto => 'BOL',
        };
    }
}
