<?php

namespace App\Exceptions\Domain;

class ShipmentFinalizedException extends ShipmentException
{
    protected $message = 'Este envio não pode mais ser alterado.';
}
