<?php

return [
    'two_factor_enabled' => env('ADMIN_TWO_FACTOR_ENABLED', env('APP_ENV') === 'production'),
    'two_factor_ttl_minutes' => 10,
    'two_factor_max_attempts' => 5,
];
