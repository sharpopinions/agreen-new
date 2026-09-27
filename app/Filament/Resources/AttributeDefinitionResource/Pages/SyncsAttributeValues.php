<?php

namespace App\Filament\Resources\AttributeDefinitionResource\Pages;

use App\Models\AttributeDefinition;
use App\Models\AttributeValue;
use App\Models\Language;

/**
 * Значення характеристики редагуються списком на тій самій сторінці (value_rows):
 * name_{мова} — назва, raw — число/HEX (однакове для всіх мов, зберігається в translations.value).
 */
trait SyncsAttributeValues
{
    protected function fillValueRows(array $data): array
    {
        $data['value_rows'] = $this->record->values()->with('translations')->get()
            ->mapWithKeys(fn(AttributeValue $v) => ['v' . $v->id => [
                'id'  => $v->id,
                'raw' => $v->translations->first()?->value,
                ...$this->getActiveLanguages()->mapWithKeys(fn(Language $l) => [
                    'name_' . $l->code => $v->translations->firstWhere('language_id', $l->id)?->name ?? '',
                ])->all(),
            ]])->all();

        return $data;
    }

    protected function syncValueRows(): void
    {
        /** @var AttributeDefinition $definition */
        $definition = $this->record;
        $languages  = $this->getActiveLanguages();

        // Так / Ні для логічного типу — автоматично
        $rows = $definition->type === 'boolean'
            ? $this->booleanRows($definition, $languages)
            : array_values($this->data['value_rows'] ?? []);

        $keep = [];
        foreach ($rows as $i => $row) {
            $value = ! empty($row['id'])
                ? $definition->values()->find($row['id'])
                : null;
            $value ??= $definition->values()->create([]);
            $value->update(['sort_order' => $i]);
            $keep[] = $value->id;

            $default = trim((string) ($row['name_' . ($languages->firstWhere('is_default', true)?->code ?? 'uk')] ?? ''));
            foreach ($languages as $lang) {
                $name = trim((string) ($row['name_' . $lang->code] ?? ''));
                $value->translations()->updateOrCreate(
                    ['language_id' => $lang->id],
                    ['name' => $name !== '' ? $name : $default, 'value' => $row['raw'] ?? null],
                );
            }
        }

        // Прибрані зі списку значення видаляються разом із привʼязками до товарів
        $definition->values()->whereNotIn('id', $keep)->get()->each->delete();
    }

    private function booleanRows(AttributeDefinition $definition, $languages): array
    {
        $existing = $definition->values()->pluck('id')->values();
        $labels = ['uk' => ['Так', 'Ні'], 'en' => ['Yes', 'No'], 'pl' => ['Tak', 'Nie']];

        return collect([0, 1])->map(fn($i) => [
            'id'  => $existing[$i] ?? null,
            'raw' => $i === 0 ? '1' : '0',
            ...$languages->mapWithKeys(fn($l) => ['name_' . $l->code => $labels[$l->code][$i] ?? $labels['en'][$i]])->all(),
        ])->all();
    }
}
