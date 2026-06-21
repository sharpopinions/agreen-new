<?php

namespace App\Filament\Resources\LanguageResource\Pages;

use App\Filament\Resources\LanguageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLanguage extends CreateRecord
{
    protected static string $resource = LanguageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['store_id'] = 1;
        return $data;
    }
}
