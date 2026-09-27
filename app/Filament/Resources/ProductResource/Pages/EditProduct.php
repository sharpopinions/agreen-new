<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Actions\TranslateAction;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    use HandlesTranslations {
        mutateFormDataBeforeFill as fillTranslations;
        afterSave as saveTranslationsAfterSave;
    }
    use SyncsProductAttributes;

    protected static string $resource = ProductResource::class;

    protected array $translationFields = ['name', 'slug', 'description', 'warning_text', 'meta_title', 'meta_description'];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->fillAttributeRows($this->fillTranslations($data));
    }

    protected function afterSave(): void
    {
        $this->saveTranslationsAfterSave();
        $this->syncAttributeRows();
    }

    protected function getHeaderActions(): array
    {
        return [
            TranslateAction::makeForPage($this->translationFields),
            Actions\DeleteAction::make(),
        ];
    }
}
