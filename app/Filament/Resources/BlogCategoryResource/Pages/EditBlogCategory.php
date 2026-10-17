<?php

namespace App\Filament\Resources\BlogCategoryResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\BlogCategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditBlogCategory extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = BlogCategoryResource::class;

    protected array $translationFields = ['name', 'slug'];
}
