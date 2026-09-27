<?php

namespace App\Filament\Resources\OrderStatusResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\OrderStatusResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderStatus extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = OrderStatusResource::class;

    protected array $translationFields = ['name'];
}
