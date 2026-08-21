<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AdminResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Redefinição de senha — Administração Malu Store')
            ->line('Recebemos uma solicitação para redefinir a senha do painel administrativo.')
            ->action('Redefinir senha', $url)
            ->line('Se você não solicitou esta alteração, nenhuma ação é necessária.');
    }
}
