<?php

namespace App\Filament\Resources\StockStatusResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\StockStatusResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStockStatus extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = StockStatusResource::class;

    protected array $translationFields = ['name'];
}
