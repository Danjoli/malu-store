<?php

namespace App\Exceptions\Domain;

class ShippingServiceNotFoundException extends ShipmentException
{
    protected $message = 'Serviço de frete não encontrado.';
}
