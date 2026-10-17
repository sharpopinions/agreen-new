<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = PageResource::class;

    protected array $translationFields = ['title', 'slug', 'blocks', 'meta_title', 'meta_description'];
}
