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

    // Uma fila acima deste atraso provavelmente está sem worker ativo.
    'maximum_queue_delay_seconds' => (int) env('ALERT_QUEUE_DELAY_SECONDS', 600),

    // Limite combinado das filas prioritária e padrão antes de alertar.
    'maximum_pending_jobs' => (int) env('ALERT_QUEUE_PENDING_JOBS', 100),

    'monitored_queues' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('ALERT_QUEUE_NAMES', 'critical,default')),
    ))),

    // Evita repetir o mesmo alerta a cada execução do agendador.
    'throttle_minutes' => (int) env('ALERT_THROTTLE_MINUTES', 60),
];
