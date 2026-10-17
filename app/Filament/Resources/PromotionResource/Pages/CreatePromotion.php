<?php

namespace App\Filament\Resources\PromotionResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\PromotionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePromotion extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = PromotionResource::class;

    protected array $translationFields = ['title', 'slug', 'description', 'meta_title', 'meta_description'];
}
