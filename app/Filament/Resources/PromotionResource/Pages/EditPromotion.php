<?php

namespace App\Filament\Resources\PromotionResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\PromotionResource;
use Filament\Resources\Pages\EditRecord;

class EditPromotion extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = PromotionResource::class;

    protected array $translationFields = ['title', 'slug', 'description', 'meta_title', 'meta_description'];
}
