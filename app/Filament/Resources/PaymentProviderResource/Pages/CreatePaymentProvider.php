<?php

namespace App\Filament\Resources\PaymentProviderResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\PaymentProviderResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentProvider extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = PaymentProviderResource::class;

    protected array $translationFields = ['name', 'description'];
}
