<?php

namespace App\Filament\Support;

use App\Models\Language;
use Filament\Forms;

/**
 * Вкладки перекладів ({code}_{field}) для довідників — у парі з HandlesTranslations на сторінках.
 * $fields: ['name' => true, 'description' => false] — поле => обов'язкове для мови за замовчуванням.
 */
class TranslationTabs
{
    public static function make(array $fields): Forms\Components\Tabs
    {
        return Forms\Components\Tabs::make(__('admin.sections.translations'))
            ->tabs(
                Language::query()->where('is_active', true)->orderBy('id')->get()
                    ->map(fn(Language $lang) => Forms\Components\Tabs\Tab::make($lang->name)->schema(
                        collect($fields)->map(fn(bool $required, string $field) => Forms\Components\TextInput::make($lang->code . '_' . $field)
                            ->label(__('admin.fields.' . $field) . ' (' . $lang->code . ')')
                            ->required($required && $lang->is_default)
                            ->maxLength(255))->values()->all()
                    ))->all()
            )
            ->columnSpanFull();
    }
}
