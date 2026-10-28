<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminTwoFactorCodeNotification extends Notification
{
    public function __construct(private readonly string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Código de acesso ao painel Malu Store')
            ->greeting('Confirmação de acesso')
            ->line('Use o código abaixo para concluir seu acesso ao painel administrativo:')
            ->line($this->code)
            ->line('O código expira em 10 minutos. Se você não tentou entrar, altere sua senha imediatamente.');
    }
}
