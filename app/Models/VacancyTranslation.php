<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacancyTranslation extends Model
{
    protected $fillable = ['vacancy_id', 'language_id', 'title', 'department', 'city', 'employment', 'description', 'requirements'];

    protected $casts = ['requirements' => 'array'];
}
