<?php

namespace App\Filament\Concerns;

use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HandlesTranslations
{
    // Клас що використовує трейт повинен оголосити: protected array $translationFields = [...];

    protected function getActiveLanguages(): Collection
    {
        return Language::query()
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
                // Масиви (блоки сторінки) — порожній список, а не ''
                $data[$lang->code . '_' . $field] = $t?->$field ?? (in_array($field, self::ARRAY_TRANSLATION_FIELDS, true) ? [] : '');
            }
        }

        return $data;
    }

    /** Поля перекладу-масиви (JSON): блоки сторінки, цифри бренду. */
    private const ARRAY_TRANSLATION_FIELDS = ['blocks', 'stats', 'program', 'speakers'];

    /** Перевірені й підготовлені дані форми (dehydrated): файли — шляхами, а не тимчасовими обʼєктами. */
    protected ?array $translationState = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->translationState = $data;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->translationState = $data;

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
                $state = $this->translationState ?? $this->data;
                $translationData[$field] = $state[$lang->code . '_' . $field] ?? '';
                if (in_array($field, self::ARRAY_TRANSLATION_FIELDS, true)) {
                    $translationData[$field] = array_values((array) ($translationData[$field] ?: []));
                }
            }

            if (array_key_exists('slug', $translationData)) {
                $translationData['slug'] = $this->resolveSlug($lang->id, $translationData['slug'], $translationData['name'] ?? $translationData['title'] ?? '');
            }

            $this->record->translations()->updateOrCreate(
                ['language_id' => $lang->id],
                $translationData
            );
        }
    }

    /**
     * Порожній slug → генеруємо з назви; немає назви → NULL (не '').
     * Згенерований slug робимо унікальним у межах мови (-2, -3 …).
     */
    private function resolveSlug(int $languageId, ?string $slug, ?string $name): ?string
    {
        $slug = trim((string) $slug);
        if ($slug !== '') {
            return $slug;
        }

        $base = Str::slug((string) $name);
        if ($base === '') {
            return null;
        }

        $translations = $this->record->translations();
        $foreignKey   = $translations->getForeignKeyName();
        $model        = $translations->getRelated();

        $candidate = $base;
        for ($i = 2; $model->newQuery()
            ->where('language_id', $languageId)
            ->where('slug', $candidate)
            ->where($foreignKey, '!=', $this->record->getKey())
            ->exists(); $i++) {
            $candidate = "{$base}-{$i}";
        }

        return $candidate;
    }
}
