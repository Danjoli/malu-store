<?php

namespace App\Exceptions\Domain;

class UnauthenticatedCheckoutException extends CheckoutException
{
    protected $message = 'Usuário não autenticado.';
}
