<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    use HandlesTranslations { afterCreate as saveTranslationsAfterCreate; }
    use SyncsProductAttributes;

    protected static string $resource = ProductResource::class;

    protected array $translationFields = ['name', 'slug', 'description', 'warning_text', 'meta_title', 'meta_description'];

    protected function afterCreate(): void
    {
        $this->saveTranslationsAfterCreate();
        $this->syncAttributeRows();
    }
}
