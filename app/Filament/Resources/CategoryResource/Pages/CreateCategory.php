<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    use HandlesTranslations { afterCreate as saveTranslationsAfterCreate; }
    use SyncsCategoryFilters;

    protected static string $resource = CategoryResource::class;

    protected array $translationFields = ['name', 'slug', 'short_description', 'description', 'meta_title', 'meta_description'];

    protected function afterCreate(): void
    {
        $this->saveTranslationsAfterCreate();
        $this->syncFilterRows();
    }
}
