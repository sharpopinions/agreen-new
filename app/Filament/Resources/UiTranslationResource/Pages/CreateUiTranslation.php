<?php

namespace App\Filament\Resources\UiTranslationResource\Pages;

use App\Filament\Resources\UiTranslationResource;
use App\Models\Language;
use App\Services\DatabaseTranslationLoader;
use Filament\Resources\Pages\CreateRecord;

class CreateUiTranslation extends CreateRecord
{
    protected static string $resource = UiTranslationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['store_id'] = 1;
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->saveValues();
        DatabaseTranslationLoader::clearCache();
    }

    private function saveValues(): void
    {
        $languages = Language::where('store_id', 1)->where('is_active', true)->get();

        foreach ($languages as $lang) {
            $value = $this->data['value_' . $lang->code] ?? null;
            if ($value !== null && $value !== '') {
                $this->record->values()->updateOrCreate(
                    ['locale' => $lang->code],
                    ['value' => $value]
                );
            }
        }
    }
}
