<?php

namespace App\Filament\Resources\UiTranslationResource\Pages;

use App\Filament\Resources\UiTranslationResource;
use App\Models\Language;
use App\Services\DatabaseTranslationLoader;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUiTranslation extends EditRecord
{
    protected static string $resource = UiTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $values = $this->record->values->keyBy('locale');

        $languages = Language::query()->where('is_active', true)->get();
        foreach ($languages as $lang) {
            $data['value_' . $lang->code] = $values->get($lang->code)?->value ?? '';
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $languages = Language::query()->where('is_active', true)->get();

        foreach ($languages as $lang) {
            $value = $this->data['value_' . $lang->code] ?? null;
            $this->record->values()->updateOrCreate(
                ['locale' => $lang->code],
                ['value' => $value ?? '']
            );
        }

        DatabaseTranslationLoader::clearCache();
    }
}
