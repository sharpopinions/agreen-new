<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...UserResource::requestActions(Actions\Action::class),
            Actions\DeleteAction::make()
                ->hidden(fn(User $record) => $record->is(auth()->user())),
        ];
    }
}
