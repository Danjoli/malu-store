<?php

namespace App\Services;

use App\Notifications\CriticalOperationalAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class OperationalAlertService
{
    /**
     * Envia um alerta pequeno e seguro sem comprometer o fluxo que já falhou.
     *
     * @param  array<string, string|int|null>  $context
     */
    public function critical(string $title, array $context = []): void
    {
        $recipient = config('alerts.email');

        if (! is_string($recipient) || $recipient === '') {
            Log::warning('Alerta operacional não enviado: ALERT_EMAIL não configurado.', [
                'title' => $title,
            ]);

            return;
        }

        try {
            Notification::route('mail', $recipient)
                ->notify(new CriticalOperationalAlert($title, $context));
        } catch (Throwable $exception) {
            // Uma falha no e-mail não pode ocultar a falha original da operação.
            Log::warning('Não foi possível enviar alerta operacional por e-mail.', [
                'title' => $title,
                'exception' => $exception::class,
            ]);
        }
    }
}
