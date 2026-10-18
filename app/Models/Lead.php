<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

/** Заявка з форми сайту. */
class Lead extends Model
{
    use BelongsToStore;

    /** Типи форм. Поля й тексти кожної — в App\Http\Controllers\LeadController. */
    public const TYPES = ['contact', 'quick_order', 'stock_notify', 'price_request', 'partner', 'service', 'vacancy', 'event', 'support'];

    /** Типи, що приходять лише з кабінету (не через публічну форму). */
    public const ACCOUNT_TYPES = ['support'];

    public const STATUSES = ['new', 'in_progress', 'done', 'spam'];

    protected $attributes = [
        'status' => 'new',
    ];

    protected $fillable = [
        'store_id', 'type', 'status', 'user_id', 'product_id',
        'name', 'phone', 'email', 'company', 'message', 'payload',
        'source_url', 'ip', 'manager_note', 'manager_id', 'processed_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'processed_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
