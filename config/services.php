<?php

$asaasEnvironment = env('ASAAS_ENV', 'sandbox');
$melhorEnvioEnvironment = env('MELHOR_ENVIO_ENV', 'sandbox');

$asaasProfiles = [
    'sandbox' => [
        // Mantém compatibilidade com a chave local existente durante a migração.
        'api_key' => env('ASAAS_SANDBOX_API_KEY') ?: env('ASAAS_API_KEY'),
        'base_url' => env('ASAAS_SANDBOX_BASE_URL', 'https://api-sandbox.asaas.com/v3'),
        'webhook_token' => env('ASAAS_SANDBOX_WEBHOOK_TOKEN') ?: env('ASAAS_WEBHOOK_TOKEN'),
    ],
    'production' => [
        'api_key' => env('ASAAS_PRODUCTION_API_KEY'),
        'base_url' => env('ASAAS_PRODUCTION_BASE_URL', 'https://api.asaas.com/v3'),
        'webhook_token' => env('ASAAS_PRODUCTION_WEBHOOK_TOKEN'),
    ],
];

$melhorEnvioProfiles = [
    'sandbox' => [
        // Mantém compatibilidade com o token local existente durante a migração.
        'token' => env('MELHOR_ENVIO_SANDBOX_TOKEN') ?: env('MELHOR_ENVIO_TOKEN'),
        'url' => env('MELHOR_ENVIO_SANDBOX_BASE_URL', 'https://sandbox.melhorenvio.com.br/api/v2/me/'),
        'webhook_secret' => env('MELHOR_ENVIO_SANDBOX_WEBHOOK_SECRET') ?: env('MELHOR_ENVIO_WEBHOOK_SECRET'),
    ],
    'production' => [
        'token' => env('MELHOR_ENVIO_PRODUCTION_TOKEN'),
        'url' => env('MELHOR_ENVIO_PRODUCTION_BASE_URL', 'https://melhorenvio.com.br/api/v2/me/'),
        'webhook_secret' => env('MELHOR_ENVIO_PRODUCTION_WEBHOOK_SECRET') ?: env('MELHOR_ENVIO_WEBHOOK_SECRET'),
    ],
];

if (! isset($asaasProfiles[$asaasEnvironment])) {
    throw new InvalidArgumentException('ASAAS_ENV deve ser sandbox ou production.');
}

if (! isset($melhorEnvioProfiles[$melhorEnvioEnvironment])) {
    throw new InvalidArgumentException('MELHOR_ENVIO_ENV deve ser sandbox ou production.');
}

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'asaas' => [
        'environment' => $asaasEnvironment,
        ...$asaasProfiles[$asaasEnvironment],
        'user_agent' => env('ASAAS_USER_AGENT', env('APP_NAME', 'Malu Store')."/1.0 ({$asaasEnvironment})"),
    ],

    'melhor_envio' => [
        'environment' => $melhorEnvioEnvironment,
        ...$melhorEnvioProfiles[$melhorEnvioEnvironment],
        'origin_zip' => env('MELHOR_ENVIO_ORIGIN_ZIP'),
        'sender' => [
            'name' => env('MELHOR_ENVIO_SENDER_NAME'),
            'phone' => env('MELHOR_ENVIO_SENDER_PHONE'),
            'email' => env('MELHOR_ENVIO_SENDER_EMAIL', env('MAIL_FROM_ADDRESS')),
            'document' => env('MELHOR_ENVIO_SENDER_DOCUMENT'),
            'address' => env('MELHOR_ENVIO_SENDER_ADDRESS'),
            'number' => env('MELHOR_ENVIO_SENDER_NUMBER'),
            'district' => env('MELHOR_ENVIO_SENDER_DISTRICT'),
            'city' => env('MELHOR_ENVIO_SENDER_CITY'),
            'state_abbr' => env('MELHOR_ENVIO_SENDER_STATE'),
        ],
        'user_agent' => env(
            'MELHOR_ENVIO_USER_AGENT',
            env('APP_NAME', 'Malu Store').' ('.env('MAIL_FROM_ADDRESS', 'suporte@example.com').')'
        ),
    ],

];
