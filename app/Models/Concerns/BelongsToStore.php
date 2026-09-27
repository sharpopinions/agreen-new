<?php

namespace App\Models\Concerns;

use App\Models\Store;
use App\Support\CurrentStore;
use Illuminate\Database\Eloquent\Builder;

/**
 * Архітектурне рішення №1 (docs/architecture/decisions.md): усі запити
 * автоматично обмежені поточним магазином, а нові записи отримують store_id.
 * Вимкнути для окремого запиту: Model::withoutGlobalScope('store').
 */
trait BelongsToStore
{
    public static function bootBelongsToStore(): void
    {
        static::addGlobalScope('store', function (Builder $query) {
            $query->where($query->getModel()->qualifyColumn('store_id'), CurrentStore::id());
        });

        static::creating(function ($model) {
            $model->store_id ??= CurrentStore::id();
        });
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
