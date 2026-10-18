<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Лист клієнту: заявку на бізнес-роль підтверджено або відхилено. */
class BusinessRoleDecision extends Notification
{
    private const ROLES = ['business_client' => 'Бізнес-клієнт', 'business_partner' => 'Бізнес-партнер'];

    public function __construct(public string $role, public bool $approved) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $role = self::ROLES[$this->role] ?? $this->role;

        $mail = (new MailMessage)->greeting("Вітаємо, {$notifiable->name}!");

        return $this->approved
            ? $mail->subject("Статус «{$role}» підтверджено — A-green")
                ->line("Вашу заявку на статус «{$role}» підтверджено. Бізнес-умови вже діють у вашому кабінеті.")
                ->action('Перейти до кабінету', route('account'))
            : $mail->subject('Заявка на бізнес-умови — A-green')
                ->line("На жаль, заявку на статус «{$role}» поки не підтверджено. Менеджер звʼяжеться з вами, щоб уточнити деталі.")
                ->line('Кабінет і надалі працює для вас як для клієнта.');
    }
}
