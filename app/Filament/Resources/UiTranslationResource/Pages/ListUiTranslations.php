<?php

namespace App\Filament\Resources\UiTranslationResource\Pages;

use App\Filament\Resources\UiTranslationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUiTranslations extends ListRecords
{
    protected static string $resource = UiTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
