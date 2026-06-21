<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = CategoryResource::class;

    protected array $translationFields = ['name', 'slug', 'description'];
}
