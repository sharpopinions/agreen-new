<?php

namespace App\Filament\Resources\BadgeResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\BadgeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBadge extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = BadgeResource::class;

    protected array $translationFields = ['name'];
}
