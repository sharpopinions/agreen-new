<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Лист з 6-значним кодом підтвердження email (Claude Design → AuthModal «Підтвердіть реєстрацію»). */
class EmailCode extends Notification
{
    public function __construct(public string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Код підтвердження A-green: {$this->code}")
            ->greeting('Вітаємо!')
            ->line('Ваш код підтвердження реєстрації:')
            ->line("**{$this->code}**")
            ->line('Код діє 30 хвилин. Якщо ви не реєструвалися на A-green, просто проігноруйте цей лист.');
    }
}
