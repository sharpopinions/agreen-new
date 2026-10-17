<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\BlogPostResource;
use Filament\Resources\Pages\EditRecord;

class EditBlogPost extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = BlogPostResource::class;

    protected array $translationFields = ['title', 'slug', 'excerpt', 'body', 'event_place', 'event_price', 'program', 'speakers', 'meta_title', 'meta_description'];
}
