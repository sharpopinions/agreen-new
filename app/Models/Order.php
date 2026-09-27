<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use BelongsToStore;

    protected $fillable = [
        'store_id', 'user_id', 'number', 'type', 'order_status_id',
        'customer_name', 'customer_phone', 'customer_email', 'customer_company',
        'shipping_provider_id', 'delivery_city', 'delivery_address', 'tracking_number',
        'payment_provider_id', 'payment_status',
        'comment', 'total', 'shipping_cost', 'locale', 'editable_until',
    ];

    protected $casts = [
        'total'          => 'decimal:2',
        'shipping_cost'  => 'decimal:2',
        'editable_until' => 'datetime',
    ];

    public const PAYMENT_STATUSES = ['pending', 'paid', 'refunded', 'failed'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public function shippingProvider()
    {
        return $this->belongsTo(ShippingProvider::class);
    }

    public function paymentProvider()
    {
        return $this->belongsTo(PaymentProvider::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
