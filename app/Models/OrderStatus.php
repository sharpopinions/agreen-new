<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use App\Models\Concerns\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use BelongsToStore, HasTranslatedName;

    /** Статус нового замовлення з сайту. */
    public const INITIAL = 'pending';

    protected $fillable = ['store_id', 'key', 'color', 'is_final', 'sort_order', 'is_active'];

    protected $casts = [
        'is_final'  => 'boolean',
        'is_active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(OrderStatusTranslation::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public static function initialId(): ?int
    {
        return static::where('key', self::INITIAL)->value('id');
    }

    /** [id => назва] для селектів адмінки. */
    public static function options(): array
    {
        return static::with('translations')->orderBy('sort_order')->get()
            ->mapWithKeys(fn(self $s) => [$s->id => $s->name])->all();
    }
}
