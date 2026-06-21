<?php

namespace App\Filament\Resources\StockStatusResource\Pages;

use App\Filament\Actions\TranslateAction;
use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\StockStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockStatus extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = StockStatusResource::class;

    protected array $translationFields = ['name'];

    protected function getHeaderActions(): array
    {
        return [
            TranslateAction::makeForPage($this->translationFields),
            Actions\DeleteAction::make(),
        ];
    }
}
