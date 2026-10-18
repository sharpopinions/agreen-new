<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/** Документ клієнта в кабінеті (Документообіг). Файли — на приватному диску local. */
class CustomerDocument extends Model
{
    use BelongsToStore;

    public const TYPES = ['invoice', 'act', 'waybill', 'contract', 'other'];

    public const DISK = 'local';

    protected $fillable = ['store_id', 'user_id', 'order_id', 'type', 'title', 'path', 'size', 'issued_at'];

    protected $casts = [
        'issued_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $doc) {
            if ($doc->isDirty('path') && $doc->path && Storage::disk(self::DISK)->exists($doc->path)) {
                $doc->size = Storage::disk(self::DISK)->size($doc->path);
            }
        });

        // Замінили файл — старий прибираємо
        static::updated(function (self $doc) {
            if ($doc->wasChanged('path') && ($old = $doc->getOriginal('path'))) {
                Storage::disk(self::DISK)->delete($old);
            }
        });

        static::deleted(fn(self $doc) => $doc->path && Storage::disk(self::DISK)->delete($doc->path));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function extension(): string
    {
        return strtoupper(pathinfo($this->path, PATHINFO_EXTENSION) ?: 'file');
    }
}
