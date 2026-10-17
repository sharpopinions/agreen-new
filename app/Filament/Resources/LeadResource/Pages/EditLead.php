<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Resources\Pages\EditRecord;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    /** Хто й коли обробив: менеджер призначається при першій зміні, час — при закритті. */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['manager_id'] = $this->record->manager_id ?? auth()->id();
        if (in_array($data['status'], ['done', 'spam'], true) && ! $this->record->processed_at) {
            $data['processed_at'] = now();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
