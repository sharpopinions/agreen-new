<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Демо-записи блогу з Claude Design (extra-pages.jsx → BLOG_POSTS, BlogPostPage; extra-pages9.jsx → BlogEventPage).
 * Ідемпотентний: пропускає записи й рубрики з такою самою назвою. Тексти — заглушки, замінюються в адмінці.
 */
class BlogSeeder extends Seeder
{
    private const ARTICLE = [
        'Ринок лакофарбових покриттів постійно розвивається, пропонуючи нові рішення для підвищення якості малярно-кузовних робіт. У 2026 році ми спостерігаємо кілька ключових тенденцій.',
        'Серед них — розвиток водорозчинних технологій, підвищена стійкість до ультрафіолету та нові формули для прискорення сушіння. Ці інновації дозволяють підприємствам значно скоротити час виробничого циклу.',
        'A-green представляє оновлену лінійку продукції від провідних виробників, що відповідає найновішим стандартам галузі. Наші фахівці готові допомогти у виборі оптимального рішення для вашого підприємства.',
    ];

    private const POSTS = [
        // рубрика, заголовок, дата публікації
        ['Новини ринку', 'Нові технології лакофарбових покриттів у 2026 році', '2026-04-27'],
        ['Поради', 'Безпека на виробництві: огляд засобів захисту для персоналу', '2026-04-20'],
        ['ЗМІ про нас', 'Дозуючі системи Kimberly-Clark: повний огляд лінійки 2026', '2026-04-15'],
        ['Новинки', 'Abrasives Mirka: нові позиції в асортименті A-green', '2026-04-10'],
        ['Поради', 'Як правильно вибрати абразивний матеріал для кузовного ремонту', '2026-04-05'],
        ['ЗМІ про нас', 'A-green на виставці Paint & Body 2026', '2026-04-01'],
        ['Новини ринку', 'Зміни у системі сертифікації хімічної продукції в Україні', '2026-03-25'],
        ['Новинки', 'Нові засоби гігієни Dettol Professional у нашому каталозі', '2026-03-20'],
    ];

    public function run(): void
    {
        $uk = Language::where('code', 'uk')->value('id');
        if (! $uk) {
            return;
        }

        $categories = [];
        foreach (['Новини ринку', 'Поради', 'ЗМІ про нас', 'Новинки', 'Події'] as $i => $name) {
            $category = BlogCategory::whereHas('translations', fn($q) => $q->where('name', $name))->first();
            if (! $category) {
                $category = BlogCategory::create(['sort_order' => $i, 'is_active' => true]);
                $category->translations()->create(['language_id' => $uk, 'name' => $name, 'slug' => Str::slug($name)]);
            }
            $categories[$name] = $category->id;
        }

        foreach (self::POSTS as $i => [$category, $title, $date]) {
            $body = $i === 0
                ? self::ARTICLE
                : ['Детальний матеріал готується. ' . self::ARTICLE[0]];
            $this->post($uk, [
                'blog_category_id' => $categories[$category],
                'type'             => 'article',
                'published_at'     => Carbon::parse($date . ' 10:00'),
            ], $title, $body);
        }

        // Подія — через місяць від запуску сідера, щоб форма реєстрації була активна
        $start = now()->addMonth()->setTime(10, 0);
        $this->post($uk, [
            'blog_category_id' => $categories['Події'],
            'type'             => 'event',
            'published_at'     => now()->subDay(),
            'event_starts_at'  => $start,
            'event_ends_at'    => $start->copy()->setTime(17, 0),
            'event_seats'      => 30,
        ], 'Воркшоп: Сучасні технології фарбування 2026', [
            'Запрошуємо професіоналів галузі малярно-кузовного ремонту на щорічний воркшоп A-green, який цього року присвячений новим технологіям лакофарбових покриттів та підготовці поверхонь.',
            'Воркшоп охопить найактуальніші теми галузі: впровадження водорозчинних технологій, нові засоби абразивної підготовки, оптимізація процесів полірування, вибір засобів захисту персоналу.',
            'Участь у воркшопі — це можливість поспілкуватися з провідними експертами галузі, отримати практичні навички та сертифікат після завершення курсу.',
        ], [
            'event_place' => 'Київ',
            'event_price' => 'Безкоштовно',
            'program'     => [
                ['time' => '10:00 – 11:00', 'topic' => 'Реєстрація. Кава-брейк'],
                ['time' => '11:00 – 12:30', 'topic' => 'Нові технології фарбування 2026'],
                ['time' => '12:30 – 13:30', 'topic' => 'Обід'],
                ['time' => '13:30 – 15:00', 'topic' => 'Практикум: абразивна підготовка'],
                ['time' => '15:00 – 15:30', 'topic' => 'Перерва'],
                ['time' => '15:30 – 17:00', 'topic' => 'Q&A. Сертифікація'],
            ],
            'speakers'    => [
                ['name' => 'Олександр Петренко', 'role' => 'Технічний директор A-green'],
                ['name' => 'Марина Коваль', 'role' => 'Представник виробника ЛФМ'],
                ['name' => 'Ігор Сидоренко', 'role' => 'Сертифікований майстер'],
            ],
        ]);
    }

    private function post(int $languageId, array $attributes, string $title, array $paragraphs, array $translation = []): void
    {
        if (BlogPost::whereHas('translations', fn($q) => $q->where('title', $title))->exists()) {
            return;
        }

        $post = BlogPost::create($attributes + ['is_active' => true]);
        $post->translations()->create($translation + [
            'language_id' => $languageId,
            'title'       => $title,
            'slug'        => Str::slug($title),
            'excerpt'     => Str::limit($paragraphs[0], 160),
            'body'        => collect($paragraphs)->map(fn($p) => '<p>' . e($p) . '</p>')->implode(''),
        ]);
    }
}
