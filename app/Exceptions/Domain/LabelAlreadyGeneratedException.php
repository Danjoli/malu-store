<?php

namespace App\Exceptions\Domain;

class LabelAlreadyGeneratedException extends ShipmentException
{
    protected $message = 'Etiqueta já foi gerada!';
}
