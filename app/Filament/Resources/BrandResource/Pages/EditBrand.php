<?php

namespace App\Filament\Resources\BrandResource\Pages;

use App\Filament\Actions\TranslateAction;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBrand extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = BrandResource::class;

    protected array $translationFields = ['name', 'slug', 'short_description', 'description', 'stats', 'meta_title', 'meta_description'];

    protected function getHeaderActions(): array
    {
        return [
            TranslateAction::makeForPage(['name', 'slug', 'short_description', 'description', 'meta_title', 'meta_description']),
            Actions\DeleteAction::make(),
        ];
    }
}
