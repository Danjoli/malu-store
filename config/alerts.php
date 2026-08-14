<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Destinatário de alertas operacionais
    |--------------------------------------------------------------------------
    |
    | Este endereço recebe apenas alertas críticos da operação. Ele não deve
    | ser usado para e-mails transacionais enviados aos clientes.
    |
    */
    'email' => env('ALERT_EMAIL'),
];
