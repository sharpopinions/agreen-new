<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Vacancy;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

/** Вакансії (Claude Design → VacanciesPage). Відгуки — заявки типу vacancy. */
class VacancyController extends Controller
{
    public function index(): Response
    {
        $langId = Language::currentId();

        $vacancies = Vacancy::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (Vacancy $v) {
                $t = $v->translations->first();

                return [
                    'id'           => $v->id,
                    'title'        => $t?->title ?? '',
                    'department'   => $t?->department,
                    'city'         => $t?->city,
                    'employment'   => $t?->employment,
                    'description'  => $t?->description,
                    'requirements' => collect($t?->requirements ?? [])->map(fn($r) => is_array($r) ? ($r['text'] ?? '') : $r)->filter()->values(),
                ];
            })
            ->filter(fn($v) => $v['title'] !== '')
            ->values();

        return Inertia::render('Vacancies', [
            'vacancies' => $vacancies,
            'seo'       => Seo::make('Вакансії', 'Приєднуйтесь до команди A-green: відкриті вакансії та умови роботи.'),
        ]);
    }
}
