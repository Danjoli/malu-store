<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HTTP Strict Transport Security
    |--------------------------------------------------------------------------
    |
    | Enviado somente em conexões HTTPS. Mantenha habilitado em produção depois
    | de confirmar que o domínio e seus subdomínios usam certificados válidos.
    |
    */
    'hsts_enabled' => env('SECURITY_HSTS', true),
    'hsts_max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),
];
