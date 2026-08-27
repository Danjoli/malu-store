<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case WaitingPost = 'waiting_post';
    case Shipped = 'shipped';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';
    case Failed = 'failed';
    case Problem = 'problem';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Aguardando pagamento',
            self::Processing => 'Em processamento',
            self::WaitingPost => 'Aguardando postagem',
            self::Shipped => 'Postado',
            self::InTransit => 'Em trânsito',
            self::Delivered => 'Entregue',
            self::Failed => 'Falha na entrega',
            self::Problem => 'Problema no envio',
            self::Cancelled => 'Cancelado',
        };
    }

    public function isFinal(): bool
    {
        return match ($this) {
            self::Delivered, self::Cancelled => true,
            default => false,
        };
    }
}
