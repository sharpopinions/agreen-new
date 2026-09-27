<?php

namespace App\Filament\Support;

use App\Models\Language;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Поле slug для вкладки перекладу: унікальне в межах мови.
 * Якщо лишити порожнім — згенерується з назви (HandlesTranslations).
 */
class SlugField
{
    public static function make(Language $lang, string $table, string $foreignKey): TextInput
    {
        return TextInput::make($lang->code . '_slug')
            ->label(__('admin.fields.slug') . ' (' . $lang->code . ')')
            ->helperText(__('admin.hints.slug'))
            ->maxLength(255)
            ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
            ->validationMessages(['regex' => __('admin.hints.slug_format')])
            ->rule(fn(?Model $record) => Rule::unique($table, 'slug')
                ->where('language_id', $lang->id)
                ->when($record, fn($rule) => $rule->whereNot($foreignKey, $record->getKey())));
    }
}
