<?php

namespace App\Exceptions\Domain;

class OrderNotPaidException extends ShipmentException
{
    protected $message = 'Pedido ainda não foi pago.';
}
