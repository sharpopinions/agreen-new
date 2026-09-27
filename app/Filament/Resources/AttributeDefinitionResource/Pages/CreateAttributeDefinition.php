<?php

namespace App\Filament\Resources\AttributeDefinitionResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\AttributeDefinitionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttributeDefinition extends CreateRecord
{
    use HandlesTranslations { afterCreate as saveTranslationsAfterCreate; }
    use SyncsAttributeValues;

    protected static string $resource = AttributeDefinitionResource::class;

    protected array $translationFields = ['name'];

    protected function afterCreate(): void
    {
        $this->saveTranslationsAfterCreate();
        $this->syncValueRows();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
