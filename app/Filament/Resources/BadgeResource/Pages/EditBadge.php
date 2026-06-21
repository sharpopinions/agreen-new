<?php

namespace App\Filament\Resources\BadgeResource\Pages;

use App\Filament\Actions\TranslateAction;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\BadgeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBadge extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = BadgeResource::class;

    protected array $translationFields = ['name'];

    protected function getHeaderActions(): array
    {
        return [
            TranslateAction::makeForPage($this->translationFields),
            Actions\DeleteAction::make(),
        ];
    }
}
