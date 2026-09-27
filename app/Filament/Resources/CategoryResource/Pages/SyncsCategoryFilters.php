<?php

namespace App\Filament\Resources\CategoryResource\Pages;

/** Фільтри категорії у формі (filter_rows) ↔ category_attribute_definitions (вигляд і порядок). */
trait SyncsCategoryFilters
{
    protected function fillFilterRows(array $data): array
    {
        $data['filter_rows'] = $this->record->attributeDefinitions()->get()
            ->map(fn($d) => [
                'attribute_definition_id' => (string) $d->id,
                'display_type'            => $d->pivot->display_type,
            ])->all();

        return $data;
    }

    protected function syncFilterRows(): void
    {
        $sync = collect(array_values($this->data['filter_rows'] ?? []))
            ->filter(fn($row) => ! empty($row['attribute_definition_id']))
            ->values()
            ->mapWithKeys(fn($row, $i) => [(int) $row['attribute_definition_id'] => [
                'display_type' => $row['display_type'] ?? 'checkbox',
                'sort_order'   => $i,
            ]]);

        $this->record->attributeDefinitions()->sync($sync);
    }
}
