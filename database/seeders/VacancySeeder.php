<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;

/** Демо-вакансії з Claude Design (extra-pages2.jsx → VacanciesPage). Ідемпотентний — за назвою посади. */
class VacancySeeder extends Seeder
{
    private const VACANCIES = [
        ['Менеджер з продажу B2B', 'Відділ продажів', 'Київ', 'Повна зайнятість',
            'Шукаємо досвідченого менеджера для роботи з корпоративними клієнтами. Досвід продажів B2B від 2 років.',
            ['Досвід активних продажів B2B від 2 років', 'Знання ринку промислових товарів буде перевагою', 'Комунікабельність, цілеспрямованість', 'Водійські права кат. B']],
        ['Технічний спеціаліст', 'Технічний відділ', 'Київ', 'Повна зайнятість',
            'Технічна підтримка клієнтів, монтаж та обслуговування обладнання. Відрядження по Україні.',
            ['Технічна освіта (механіка, електроніка)', 'Досвід роботи з промисловим обладнанням', 'Наявність водійських прав', 'Готовність до відряджень']],
        ['Логіст / комірник', 'Складська служба', 'Київ', 'Повна зайнятість',
            'Організація складської логістики, прийом та відпуск товару, робота з WMS-системою.',
            ['Досвід роботи на складі від 1 року', 'Знання 1С або аналогів', 'Відповідальність, уважність до деталей', 'Фізична витривалість']],
        ['Маркетолог / SMM', 'Маркетинг', 'Київ / Remote', 'Повна зайнятість / Remote',
            'Розробка та реалізація маркетингової стратегії, ведення соціальних мереж, email-маркетинг.',
            ['Досвід у digital-маркетингу від 2 років', 'Знання Google Ads, Meta Ads', 'Навички написання текстів', 'Знання англійської мови']],
    ];

    public function run(): void
    {
        $uk = Language::where('code', 'uk')->value('id');
        if (! $uk) {
            return;
        }

        foreach (self::VACANCIES as $i => [$title, $department, $city, $employment, $description, $requirements]) {
            if (Vacancy::whereHas('translations', fn($q) => $q->where('title', $title))->exists()) {
                continue;
            }
            $vacancy = Vacancy::create(['sort_order' => $i, 'is_active' => true]);
            $vacancy->translations()->create(compact('title', 'department', 'city', 'employment', 'description', 'requirements') + [
                'language_id' => $uk,
            ]);
        }
    }
}
