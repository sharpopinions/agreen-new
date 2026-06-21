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

    protected array $translationFields = ['name', 'slug', 'description'];

    protected function getHeaderActions(): array
    {
        return [
            TranslateAction::makeForPage($this->translationFields),
            Actions\DeleteAction::make(),
        ];
    }
}
