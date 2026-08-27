<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case Failed = 'failed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::PendingPayment => 'Aguardando pagamento',
            self::Paid => 'Pago',
            self::Failed => 'Pagamento recusado',
            self::Expired => 'Pagamento vencido',
            self::Cancelled => 'Cancelado',
            self::Shipped => 'Enviado',
            self::Delivered => 'Entregue',
        };
    }
}
