<?php

namespace App\Exceptions\Domain;

class ShippingSenderConfigurationException extends ShipmentException
{
    protected $message = 'Configure os dados do remetente da Melhor Envio antes de gerar a etiqueta.';
}
