<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\ServiceResource;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = ServiceResource::class;

    protected array $translationFields = ['title', 'slug', 'summary', 'details', 'body', 'stats', 'benefits', 'courses', 'meta_title', 'meta_description'];
}
