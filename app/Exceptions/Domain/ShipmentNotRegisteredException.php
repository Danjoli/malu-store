<?php

namespace App\Exceptions\Domain;

class ShipmentNotRegisteredException extends ShipmentException
{
    protected $message = 'Envio não existe na Melhor Envio.';
}
