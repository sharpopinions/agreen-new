<?php

namespace App\Filament\Concerns;

use App\Models\Language;
use Illuminate\Support\Collection;

trait HandlesTranslations
{
    // Клас що використовує трейт повинен оголосити: protected array $translationFields = [...];

    protected function getActiveLanguages(): Collection
    {
        return Language::where('store_id', 1)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $translations = $this->record->translations->keyBy('language_id');

        foreach ($this->getActiveLanguages() as $lang) {
            $t = $translations->get($lang->id);
            foreach ($this->translationFields as $field) {
                $data[$lang->code . '_' . $field] = $t?->$field ?? '';
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['store_id'] = 1;
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->saveTranslations();
    }

    protected function afterSave(): void
    {
        $this->saveTranslations();
    }

    public function refillFormAfterTranslation(): void
    {
        $this->record = $this->record->fresh();
        $this->record->load('translations');
        $this->fillForm();
    }

    private function saveTranslations(): void
    {
        foreach ($this->getActiveLanguages() as $lang) {
            $translationData = [];
            foreach ($this->translationFields as $field) {
                $translationData[$field] = $this->data[$lang->code . '_' . $field] ?? '';
            }

            $this->record->translations()->updateOrCreate(
                ['language_id' => $lang->id],
                $translationData
            );
        }
    }
}
