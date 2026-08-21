<?php

namespace App\Exceptions\Domain;

class EmptyCartException extends CheckoutException
{
    protected $message = 'Carrinho vazio.';
}
