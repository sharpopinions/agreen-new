<?php

namespace App\Models\Concerns;

use App\Models\Language;
use Illuminate\Database\Eloquent\Model;

/**
 * Назва/опис довідника поточною мовою з запасним варіантом — перший наявний переклад.
 * Модель має мати зв'язок translations() з полем language_id.
 */
trait HasTranslatedName
{
    public function currentTranslation(): ?Model
    {
        $translations = $this->translations;

        return $translations->firstWhere('language_id', Language::currentId()) ?? $translations->first();
    }

    public function getNameAttribute(): string
    {
        return $this->currentTranslation()?->name ?? ($this->attributes['key'] ?? $this->attributes['driver'] ?? '');
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->currentTranslation()?->description;
    }
}
