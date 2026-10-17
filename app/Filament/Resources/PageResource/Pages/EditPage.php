<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    use HandlesTranslations;

    protected static string $resource = PageResource::class;

    protected array $translationFields = ['title', 'slug', 'blocks', 'meta_title', 'meta_description'];

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')
                ->label(__('admin.actions.view_on_site'))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn() => ($slug = $this->record->translations()->where('language_id', \App\Models\Language::currentId())->value('slug')) ? url('/' . $slug) : null)
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()->hidden(fn() => $this->record->key !== null),
        ];
    }
}
