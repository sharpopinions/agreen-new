<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const DELIVERY_METHODS = [
        'nova_poshta' => 'Нова Пошта',
        'ukrposhta'   => 'Укрпошта',
        'justin'      => 'Justin',
        'meest'       => 'Meest Express',
        'pickup'      => 'Самовивіз',
        'courier'     => "Кур'єр",
    ];

    public const PAYMENT_METHODS = [
        'card_transfer'   => 'Оплата на карту',
        'online'          => 'Онлайн оплата',
        'invoice_vat'     => 'Рахунок-фактура з ПДВ',
        'invoice_no_vat'  => 'Рахунок-фактура без ПДВ',
        'courier_cash'    => "Оплата кур'єру",
        'cash_on_delivery'=> 'Післяплата',
    ];

    public const STATUSES = [
        'new'        => 'Нове',
        'processing' => 'В обробці',
        'shipped'    => 'Відправлено',
        'completed'  => 'Виконано',
        'cancelled'  => 'Скасовано',
    ];

    protected $fillable = [
        'store_id', 'number', 'type', 'status',
        'customer_name', 'customer_phone', 'customer_email', 'customer_company',
        'delivery_method', 'delivery_city', 'delivery_address',
        'payment_method', 'comment', 'total', 'locale',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPreorder(): bool
    {
        return $this->type === 'preorder';
    }

    /** Номер виду A-2026-00042. */
    public static function makeNumber(int $id): string
    {
        return sprintf('A-%s-%05d', now()->format('Y'), $id);
    }
}
