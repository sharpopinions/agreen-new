<?php

namespace App\Filament\Resources\BrandResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\BrandResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBrand extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = BrandResource::class;

    protected array $translationFields = ['name', 'slug', 'description'];
}
