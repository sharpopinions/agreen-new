<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Actions\TranslateAction;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    use HandlesTranslations {
        mutateFormDataBeforeFill as fillTranslations;
        afterSave as saveTranslationsAfterSave;
    }
    use SyncsCategoryFilters;

    protected static string $resource = CategoryResource::class;

    protected array $translationFields = ['name', 'slug', 'short_description', 'description', 'meta_title', 'meta_description'];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->fillFilterRows($this->fillTranslations($data));
    }

    protected function afterSave(): void
    {
        $this->saveTranslationsAfterSave();
        $this->syncFilterRows();
    }

    protected function getHeaderActions(): array
    {
        return [
            TranslateAction::makeForPage($this->translationFields),
            Actions\DeleteAction::make(),
        ];
    }
}
