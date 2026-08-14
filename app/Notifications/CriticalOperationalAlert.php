<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CriticalOperationalAlert extends Notification
{
    use Queueable;

    /**
     * @param  array<string, string|int|null>  $context
     */
    public function __construct(
        private readonly string $title,
        private readonly array $context = [],
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->error()
            ->subject('Alerta crítico - Malu Store')
            ->greeting('Atenção: ocorreu uma falha crítica')
            ->line($this->title)
            ->line('Horário: '.now()->format('d/m/Y H:i:s'));

        foreach ($this->context as $label => $value) {
            if ($value !== null && $value !== '') {
                $message->line("{$label}: {$value}");
            }
        }

        return $message->line('Consulte os logs do Laravel para a investigação técnica.');
    }
}
