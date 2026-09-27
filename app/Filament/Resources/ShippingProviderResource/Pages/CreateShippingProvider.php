<?php

namespace App\Filament\Resources\ShippingProviderResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\ShippingProviderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShippingProvider extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = ShippingProviderResource::class;

    protected array $translationFields = ['name', 'description'];
}
