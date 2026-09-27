<?php

namespace App\Filament\Resources\AttributeDefinitionResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\AttributeDefinitionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttributeDefinition extends EditRecord
{
    use HandlesTranslations {
        mutateFormDataBeforeFill as fillTranslations;
        afterSave as saveTranslationsAfterSave;
    }
    use SyncsAttributeValues;

    protected static string $resource = AttributeDefinitionResource::class;

    protected array $translationFields = ['name'];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->fillValueRows($this->fillTranslations($data));
    }

    protected function afterSave(): void
    {
        $this->saveTranslationsAfterSave();
        $this->syncValueRows();
        // Оновити id нових значень у формі, щоб повторне збереження не дублювало їх
        $this->data['value_rows'] = $this->fillValueRows([])['value_rows'];
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
