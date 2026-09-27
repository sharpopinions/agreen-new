<?php

namespace App\Filament\Resources\ProductResource\Pages;

/** Характеристики товару у формі: рядки «характеристика → значення» (attribute_rows) ↔ product_attribute_values. */
trait SyncsProductAttributes
{
    protected function fillAttributeRows(array $data): array
    {
        $data['attribute_rows'] = $this->record->attributeValues()->get()
            ->groupBy('attribute_definition_id')
            ->map(fn($values, $definitionId) => [
                'attribute_definition_id' => $definitionId,
                'value_ids'               => $values->pluck('id')->map(fn($id) => (string) $id)->all(),
            ])
            ->values()->all();

        return $data;
    }

    protected function syncAttributeRows(): void
    {
        $ids = collect($this->data['attribute_rows'] ?? [])
            ->flatMap(fn($row) => (array) ($row['value_ids'] ?? []))
            ->map(fn($id) => (int) $id)->filter()->unique()->values();

        $this->record->attributeValues()->sync($ids);
    }
}
