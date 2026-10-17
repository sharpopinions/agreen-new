<?php

namespace App\Notifications;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Лист менеджеру про нову заявку з сайту. */
class NewLead extends Notification
{
    public function __construct(public Lead $lead) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $lead = $this->lead->loadMissing('product');
        $type = __('admin.leads.types.' . $lead->type, [], 'uk');

        $mail = (new MailMessage)
            ->subject("Нова заявка: {$type} — {$lead->name}")
            ->greeting("Нова заявка: {$type}")
            ->line("Ім'я: {$lead->name}");

        foreach (['phone' => 'Телефон', 'email' => 'Email', 'company' => 'Компанія'] as $field => $label) {
            if ($lead->$field) {
                $mail->line("{$label}: {$lead->$field}");
            }
        }
        if ($lead->product) {
            $mail->line("Товар: {$lead->product->sku}");
        }
        foreach ((array) $lead->payload as $key => $value) {
            $mail->line(__('admin.leads.payload.' . $key, [], 'uk') . ': ' . (is_scalar($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE)));
        }
        if ($lead->message) {
            $mail->line('Повідомлення: ' . $lead->message);
        }

        return $mail->action('Відкрити в адмінці', LeadResource::getUrl('view', ['record' => $lead]));
    }
}
