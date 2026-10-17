<?php

namespace App\Filament\Resources\VacancyResource\Pages;

use App\Filament\Concerns\HandlesTranslations;
use App\Filament\Resources\VacancyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVacancy extends CreateRecord
{
    use HandlesTranslations;

    protected static string $resource = VacancyResource::class;

    protected array $translationFields = ['title', 'department', 'city', 'employment', 'description', 'requirements'];
}
