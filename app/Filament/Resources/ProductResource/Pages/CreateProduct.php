<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = ProductResource::class;

    protected array $translationFields = ['name', 'slug', 'description'];
}
