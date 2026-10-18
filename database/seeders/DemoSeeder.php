<?php

namespace Database\Seeders;

use App\Models\AttributeDefinition;
use App\Models\Badge;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\CashbackTransaction;
use App\Models\Category;
use App\Models\CustomerDocument;
use App\Models\Language;
use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PaymentProvider;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\ShippingProvider;
use App\Models\StockStatus;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Демо-дані для перегляду всього сайту й адмінки: ~150 товарів (пагінація каталогу),
 * 30+ записів блогу, бренди, акції, вакансії, відгуки, клієнти з замовленнями,
 * документами, кешбеком і зверненнями, заявки з форм.
 *
 * Запуск: php artisan db:seed --class=DemoSeeder (після звичайного db:seed).
 * Повторний запуск нічого не дублює — демо-дані позначені SKU «DM-…» та email «…@demo.test».
 *
 * Облікові записи (пароль у всіх — password):
 *   admin@demo.test (адміністратор), manager@demo.test, content@demo.test,
 *   client@demo.test (клієнт з історією), business@demo.test, partner@demo.test.
 */
class DemoSeeder extends Seeder
{
    private const PASSWORD = 'password';

    private const BRANDS = [
        // назва, країна/опис
        ['Festool', 'Німецький виробник професійного інструменту'],
        ['Norton', 'Американський виробник абразивів'],
        ['Klingspor', 'Німецький виробник шліфувальних матеріалів'],
        ['Tork', 'Шведський бренд гігієнічних систем для бізнесу'],
        ['Ansell', 'Австралійський виробник захисних рукавиць'],
        ['Uvex', 'Німецький виробник засобів індивідуального захисту'],
        ['Loctite', 'Клеї та герметики від Henkel'],
        ['Tikkurila', 'Фінський виробник фарб і покриттів'],
        ['Farécla', 'Британський виробник полірувальних паст'],
        ['Menzerna', 'Німецький виробник полірувальних систем'],
        ['SATA', 'Німецький виробник фарбопультів'],
        ['Rupes', 'Італійський виробник полірувальних машин'],
    ];

    /**
     * Підкатегорія (slug uk) => [бренди, [назва uk, назва en], варіанти].
     * Варіант — частина назви, однакова для обох мов.
     */
    private const CATALOG = [
        'hanchirky-ta-servetky'   => [['tork', 'kimberly-clark'], ['Серветки протиральні', 'Wiping cloths'], ['білі 50 шт', 'сині 100 шт', 'безворсові 200 шт', 'рулон 500 шт', 'в діспенсері 90 шт', 'мікрофібра 10 шт']],
        'zasoby-dlia-prybyrannia' => [['dettol', 'tork'], ['Засіб для прибирання', 'Cleaning agent'], ['універсальний 1 л', 'для скла 0,5 л', 'знежирювач 5 л', 'для підлоги 5 л', 'дезінфекційний 1 л', 'концентрат 10 л']],
        'paperovi-rushnyky'       => [['tork', 'kimberly-clark'], ['Паперові рушники', 'Paper towels'], ['Z-складання 200 арк', 'V-складання 250 арк', 'рулон 150 м', 'рулон центральної витяжки', 'двошарові 2×100', 'тришарові преміум']],
        'shlifuvalni-dysky'       => [['mirka', '3m', 'festool', 'norton'], ['Шліфувальний диск', 'Sanding disc'], ['P80 150 мм', 'P120 150 мм', 'P240 150 мм', 'P400 150 мм', 'P80 125 мм', 'P240 125 мм', 'P400 77 мм']],
        'abrazyvni-kruhy'         => [['norton', 'klingspor'], ['Абразивний круг', 'Abrasive wheel'], ['відрізний 125 мм', 'зачисний 125 мм', 'пелюстковий P80', 'пелюстковий P120', 'відрізний 230 мм', 'фібровий P80']],
        'shlifuvalna-shkurka'     => [['klingspor', 'mirka', '3m'], ['Шліфувальна шкурка', 'Sandpaper'], ['P80 рулон 50 м', 'P120 аркуш', 'P240 аркуш', 'P400 водостійка', 'P80 смужка 70×420', 'P240 рулон 25 м']],
        'dyspensery'              => [['kimberly-clark', 'tork'], ['Диспенсер', 'Dispenser'], ['для рушників білий', 'для рушників чорний', 'для мила 1 л', 'для туалетного паперу', 'для серветок настільний', 'сенсорний для рушників']],
        'dozatory-ridyny'         => [['kimberly-clark', 'dettol'], ['Дозатор рідини', 'Liquid dispenser'], ['ліктьовий 1 л', 'сенсорний 1 л', 'настінний 0,5 л', 'для піни 1 л', 'для антисептика 1,2 л', 'нержавійка 0,8 л']],
        'avtomatychni-systemy'    => [['kimberly-clark', 'tork'], ['Автоматична система дозування', 'Automatic dosing system'], ['X Pro 5 л', 'X Pro 10 л', 'Mini 2 л', 'для мийки 20 л', 'для кухні 5 л', 'з контролером']],
        'hruntovky'               => [['novol', 'tikkurila', 'motip'], ['Ґрунтовка', 'Primer'], ['акрилова 1K сіра 1 л', 'акрилова 2K біла 1 л', 'епоксидна 2K 1 л', 'кислотна 1K 0,5 л', 'аерозоль 1K чорна 0,5 л', 'наповнювач 2K сірий 5 л']],
        'avtolaky'                => [['novol', 'motip'], ['Автолак', 'Clear coat'], ['2K HS 1 л', '2K MS 1 л', '2K UHS 5 л', 'матовий 2K 1 л', 'аерозоль 1K 0,5 л', 'швидкий 2K 5 л']],
        'farby'                   => [['tikkurila', 'motip', 'novol'], ['Фарба', 'Paint'], ['база біла 1 л', 'база чорна 1 л', 'металік сіра 1 л', 'аерозоль біла 0,5 л', 'для дисків сіра 0,5 л', 'термостійка чорна 0,5 л']],
        'mylo-ta-antyseptyki'     => [['dettol', 'tork', 'kimberly-clark'], ['Рідке мило', 'Liquid soap'], ['кремове 1 л', 'антибактеріальне 1 л', 'пінне 0,8 л', 'антисептик гель 0,5 л', 'антисептик 5 л', 'для важких забруднень 2 л']],
        'hihiienichni-rushnyky'   => [['tork', 'kimberly-clark'], ['Гігієнічні рушники', 'Hygiene towels'], ['одношарові', 'двошарові', 'рулонні 6 шт', 'листові 20 пачок', 'білі преміум', 'еко-переробка']],
        'tualetnyi-papir'         => [['tork', 'kimberly-clark'], ['Туалетний папір', 'Toilet paper'], ['джамбо 12 рулонів', 'стандарт 24 рулони', 'листовий 30 пачок', 'двошаровий 8 рулонів', 'центральна витяжка', 'преміум тришаровий']],
        'rukavytsi-zakhysni'      => [['ansell', 'uvex', '3m'], ['Рукавиці захисні', 'Protective gloves'], ['нітрилові S 100 шт', 'нітрилові M 100 шт', 'нітрилові L 100 шт', 'стійкі до порізів', 'хімостійкі', 'з покриттям PU']],
        'zakhysni-kombinezony'    => [['dupont', '3m', 'uvex'], ['Захисний комбінезон', 'Protective coverall'], ['Tyvek M', 'Tyvek L', 'Tyvek XL', 'для фарбування XXL', 'антистатичний L', 'багаторазовий XL']],
        'masky-ta-respiratory'    => [['3m', 'uvex'], ['Респіратор', 'Respirator'], ['FFP2 20 шт', 'FFP3 10 шт', 'напівмаска 6200', 'фільтр A2P3', 'повнолицьова маска', 'з клапаном FFP2']],
        'konstruktsiini-klei'     => [['sika', 'loctite', '3m'], ['Конструкційний клей', 'Structural adhesive'], ['епоксидний 50 мл', 'поліуретановий 310 мл', 'для металу 2K 50 мл', 'для пластику 25 мл', 'швидкий 2K 200 мл', 'термостійкий 50 мл']],
        'sylikonovi-hermetyky'    => [['sika', 'loctite'], ['Герметик', 'Sealant'], ['силіконовий білий 280 мл', 'силіконовий прозорий 280 мл', 'кузовний сірий 310 мл', 'термостійкий червоний 85 мл', 'поліуретановий чорний 300 мл', 'шовний 600 мл']],
        'montazhna-pina'          => [['sika', 'loctite'], ['Монтажна піна', 'Mounting foam'], ['професійна 750 мл', 'побутова 500 мл', 'зимова 750 мл', 'вогнестійка 750 мл', 'клей-піна 750 мл', 'низького розширення 750 мл']],
        'poliruvaini-mashyny'     => [['rupes', 'festool', '3m'], ['Полірувальна машина', 'Polishing machine'], ['ексцентрикова 150 мм', 'ротаційна 180 мм', 'акумуляторна 75 мм', 'LHR 21 Mark III', 'компактна 50 мм', 'з регулюванням обертів']],
        'poliruvaini-kruhy'       => [['menzerna', 'rupes', 'farecla'], ['Полірувальний круг', 'Polishing pad'], ['твердий 150 мм', 'середній 150 мм', 'м’який 150 мм', 'вовняний 150 мм', 'мікрофібра 125 мм', 'набір 3 шт']],
        'pasta-dlia-poluruvannia' => [['menzerna', 'farecla', '3m'], ['Полірувальна паста', 'Polishing compound'], ['абразивна 1 кг', 'фінішна 1 кг', 'антиголограмна 250 мл', 'One-Step 1 л', 'для пластику 250 мл', 'для фар 150 мл']],
    ];

    private const FIRST_NAMES = ['Олександр', 'Андрій', 'Дмитро', 'Сергій', 'Максим', 'Віктор', 'Ігор', 'Юрій', 'Олена', 'Наталія', 'Ірина', 'Марина', 'Тетяна', 'Оксана', 'Світлана', 'Роман', 'Василь', 'Богдан', 'Анна', 'Катерина'];
    private const LAST_NAMES  = ['Шевченко', 'Коваленко', 'Бондаренко', 'Ткаченко', 'Кравченко', 'Олійник', 'Мельник', 'Шевчук', 'Поліщук', 'Лисенко', 'Гончаренко', 'Савченко', 'Руденко', 'Мороз', 'Петренко', 'Марченко'];
    private const COMPANIES   = ['ТОВ «Автосервіс Плюс»', 'ТОВ «ProPaint»', 'ФОП Іваненко', 'ТОВ «Кузов Майстер»', 'ПП «Детейлінг Центр»', 'ТОВ «Промхім»', 'ТОВ «Клінінг Груп»', 'ТОВ «Метал-Сервіс»', 'ТОВ «Автоколор»', 'ПП «Блиск»'];
    private const CITIES      = ['Київ', 'Львів', 'Одеса', 'Дніпро', 'Харків', 'Вінниця', 'Полтава', 'Житомир', 'Черкаси', 'Рівне'];

    private int $ukId;
    private ?int $enId;

    public function run(): void
    {
        // Порожня база (не запускали db:seed) — спершу базові дані: магазин, мови, категорії, бренди…
        if (! Category::exists()) {
            $this->command?->info('Базових даних немає — запускаю DatabaseSeeder.');
            $this->call(DatabaseSeeder::class);
        }

        if (Product::where('sku', 'like', 'DM-%')->exists()) {
            $this->command?->warn('Демо-дані вже є — пропускаю.');

            return;
        }

        // Залишки невдалого попереднього запуску (демо-користувачі без товарів) — прибираємо, щоб не було дублів
        User::where('email', 'like', '%@demo.test')->delete();

        mt_srand(2026);
        $this->ukId = Language::where('code', 'uk')->value('id');
        $this->enId = Language::where('code', 'en')->value('id');

        $this->brands();
        $this->products();
        $this->blog();
        $this->promotions();
        $this->vacancies();
        $this->testimonials();
        $users = $this->users();
        $this->orders($users);
        $this->clientExtras($users['client@demo.test']);
        $this->leads($users);

        $this->command?->info('Демо-дані додано. Вхід: admin@demo.test / ' . self::PASSWORD . ', клієнт: client@demo.test / ' . self::PASSWORD);
    }

    private function pick(array $items)
    {
        return $items[mt_rand(0, count($items) - 1)];
    }

    private function chance(int $percent): bool
    {
        return mt_rand(1, 100) <= $percent;
    }

    private function brands(): void
    {
        $sort = Brand::max('sort_order') ?? 0;
        foreach (self::BRANDS as [$name, $text]) {
            $slug = Str::slug($name);
            if (Brand::whereHas('translations', fn($q) => $q->where('slug', $slug))->exists()) {
                continue;
            }
            $brand = Brand::create(['sort_order' => ++$sort, 'is_active' => true]);
            foreach (array_filter([$this->ukId, $this->enId]) as $lang) {
                $brand->translations()->create([
                    'language_id'       => $lang,
                    'name'              => $name,
                    'slug'              => $slug,
                    'short_description' => $text,
                    'description'       => '<p>' . e($text) . '. Офіційний партнер A-green в Україні: оригінальна продукція, консультації та сервіс.</p>',
                ]);
            }
        }
    }

    private function products(): void
    {
        $brandIds = Brand::with('translations')->get()
            ->mapWithKeys(fn($b) => [$b->translations->firstWhere('language_id', $this->ukId)?->slug => $b->id]);
        $statuses = StockStatus::pluck('id', 'code');
        $badges   = Badge::pluck('id')->all();
        $attrs    = $this->attributeValues();

        $n = 0;
        foreach (self::CATALOG as $subSlug => [$brands, [$ukBase, $enBase], $variants]) {
            $sub = Category::whereHas('translations', fn($q) => $q->where('slug', $subSlug))->first();
            if (! $sub) {
                continue;
            }

            foreach ($variants as $variant) {
                $n++;
                $brandSlug = $this->pick($brands);
                $brandName = Brand::find($brandIds[$brandSlug] ?? null)?->translations()->value('name') ?? '';
                $sku   = sprintf('DM-%04d', $n);
                $price = $this->price($subSlug);
                $roll  = mt_rand(1, 100);
                $code  = $roll <= 72 ? 'in_stock' : ($roll <= 87 ? 'on_order' : 'out_of_stock');

                $product = Product::create([
                    'brand_id'        => $brandIds[$brandSlug] ?? null,
                    'stock_status_id' => $statuses[$code] ?? $statuses->first(),
                    'sku'             => $sku,
                    'price'           => $price,
                    'old_price'       => $this->chance(25) ? round($price * (1 + mt_rand(10, 30) / 100), -1) : null,
                    'rating'          => mt_rand(35, 50) / 10,
                    'reviews_count'   => mt_rand(0, 120),
                    'stock_quantity'  => $code === 'in_stock' ? mt_rand(3, 400) : 0,
                    'preorder_days'   => $code === 'on_order' ? $this->pick([7, 10, 14, 21]) : null,
                    'sort_order'      => 100 + $n,
                    'is_active'       => true,
                ]);

                $ukName = trim("{$ukBase} {$brandName} {$variant}");
                $product->translations()->create([
                    'language_id' => $this->ukId,
                    'name'        => $ukName,
                    'slug'        => Str::slug($ukName) . '-' . strtolower($sku),
                    'description' => "{$ukName}. Професійна продукція для сервісних центрів, виробництва та клінінгу. Оригінал від офіційного дистриб’ютора, доставка по всій Україні.",
                ]);
                if ($this->enId) {
                    $enName = trim("{$brandName} {$enBase} {$variant}");
                    $product->translations()->create([
                        'language_id' => $this->enId,
                        'name'        => $enName,
                        'slug'        => Str::slug($enName) . '-' . strtolower($sku),
                        'description' => "{$enName}. Professional products for service centres, manufacturing and cleaning.",
                    ]);
                }

                $product->categories()->attach([$sub->id, $sub->parent_id]);
                if ($badges && $this->chance(30)) {
                    $product->badges()->attach($this->pick($badges));
                }
                $product->attributeValues()->syncWithoutDetaching($this->attributesFor($subSlug, $variant, $attrs));
            }
        }
    }

    private function price(string $sub): float
    {
        [$min, $max] = match (true) {
            str_contains($sub, 'mashyny'), str_contains($sub, 'avtomatychni') => [3500, 18000],
            str_contains($sub, 'dyspensery'), str_contains($sub, 'dozatory')  => [650, 4200],
            str_contains($sub, 'kombinezony'), str_contains($sub, 'lak')      => [280, 2400],
            default                                                            => [45, 1200],
        };

        return round(mt_rand($min, $max), -1) - 1;
    }

    /** [ключ => [назва значення uk => id]] з AttributeSeeder. */
    private function attributeValues(): array
    {
        $map = ['Зернистість' => 'grit', 'Тип системи' => 'system', 'Колір' => 'color', 'Обʼєм, л' => 'volume'];
        $out = [];
        foreach (AttributeDefinition::with(['translations', 'values.translations'])->get() as $def) {
            $key = $map[$def->translations->firstWhere('language_id', $this->ukId)?->name] ?? null;
            if (! $key) {
                continue;
            }
            foreach ($def->values as $value) {
                $out[$key][$value->translations->firstWhere('language_id', $this->ukId)?->name] = $value->id;
            }
        }

        return $out;
    }

    private function attributesFor(string $sub, string $variant, array $attrs): array
    {
        $ids = [];
        if (in_array($sub, ['shlifuvalni-dysky', 'abrazyvni-kruhy', 'shlifuvalna-shkurka'], true)
            && preg_match('/P(80|120|240|400)/', $variant, $m)) {
            $ids[] = $attrs['grit']['P' . $m[1]] ?? null;
        }
        if (in_array($sub, ['hruntovky', 'avtolaky', 'farby'], true)) {
            $ids[] = $attrs['system'][str_contains($variant, '2K') ? '2K' : '1K'] ?? null;
            $color = str_contains($variant, 'біла') ? 'Білий' : (str_contains($variant, 'чорна') ? 'Чорний' : (str_contains($variant, 'сір') ? 'Сірий' : null));
            if ($color) {
                $ids[] = $attrs['color'][$color] ?? null;
            }
            $volume = str_contains($variant, '5 л') ? '5 л' : (str_contains($variant, '0,5 л') ? '0,5 л' : '1 л');
            $ids[] = $attrs['volume'][$volume] ?? null;
        }

        return array_values(array_filter($ids));
    }

    private function blog(): void
    {
        $categories = BlogCategory::with('translations')->get()
            ->mapWithKeys(fn($c) => [$c->translations->first()?->name => $c->id]);
        $topics = [
            'Як підготувати поверхню до фарбування: покрокова інструкція',
            'П’ять помилок при виборі абразиву для кузовного ремонту',
            'Водорозчинні фарби: плюси й мінуси для СТО',
            'Огляд респіраторів FFP2 і FFP3: що обрати для фарбувальної камери',
            'Гігієнічні системи для офісу: як порахувати витрати',
            'Полірування без голограм: техніка трьох кроків',
            'Чим відрізняються ґрунтовки 1K і 2K',
            'Новий склад A-green у Львові: швидша доставка на заході України',
            'Як зберігати лакофарбові матеріали взимку',
            'Сертифікація майстрів: результати весняного потоку',
            'Сухе шліфування з Mirka Abranet: досвід клієнтів',
            'Клеї замість зварювання: де це працює',
            'Чек-лист охорони праці для малярної дільниці',
            'Як знизити витрату паперових рушників на 30%',
            'Огляд полірувальних машин 2026 року',
            'Корпоративні клієнти A-green: персональні умови й кешбек',
            'Герметики для кузова: який обрати',
            'Тренди автодетейлінгу 2026',
            'Інтерв’ю з технологом: як обрати лак',
            'Дозуючі системи для автомийок: економія хімії',
            'Рукавиці для роботи з розчинниками: тест п’яти моделей',
            'Як оформити замовлення з відстрочкою платежу',
        ];
        $articleCats = ['Новини ринку', 'Поради', 'ЗМІ про нас', 'Новинки'];

        foreach ($topics as $i => $title) {
            $this->post([
                'blog_category_id' => $categories[$articleCats[$i % 4]] ?? null,
                'type'             => 'article',
                'published_at'     => now()->subDays(40 + $i * 6)->setTime(10, 0),
            ], $title);
        }

        $events = [
            ['Вебінар: Полірування без голограм', 14, 'Онлайн, Zoom', 'Безкоштовно'],
            ['Майстер-клас Mirka у Львові', 25, 'Львів, навчальний центр A-green', '500 ₴'],
            ['День відкритих дверей навчального центру', 40, 'Київ, вул. Крайня, 1', 'Безкоштовно'],
            ['Семінар з охорони праці на СТО', 60, 'Дніпро', '800 ₴'],
            ['Воркшоп по ґрунтовках Novol', -30, 'Київ', 'Безкоштовно'],
            ['Презентація систем Tork для HoReCa', -75, 'Одеса', 'Безкоштовно'],
        ];
        foreach ($events as [$title, $days, $place, $price]) {
            $start = now()->addDays($days)->setTime(11, 0);
            $this->post([
                'blog_category_id' => $categories['Події'] ?? null,
                'type'             => 'event',
                'published_at'     => $days > 0 ? now()->subDays(3) : $start->copy()->subDays(20),
                'event_starts_at'  => $start,
                'event_ends_at'    => $start->copy()->setTime(15, 0),
                'event_seats'      => $this->pick([20, 30, 50]),
            ], $title, [
                'event_place' => $place,
                'event_price' => $price,
                'program'     => [
                    ['time' => '11:00 – 11:30', 'topic' => 'Реєстрація, кава'],
                    ['time' => '11:30 – 13:00', 'topic' => 'Теорія та огляд матеріалів'],
                    ['time' => '13:30 – 15:00', 'topic' => 'Практика, відповіді на питання'],
                ],
                'speakers'    => [['name' => 'Олександр Петренко', 'role' => 'Технічний директор A-green']],
            ]);
        }
    }

    private function post(array $attributes, string $title, array $translation = []): void
    {
        $text = "{$title}. Розповідаємо, на що звернути увагу, які матеріали обрати та як уникнути типових помилок. Матеріал підготували фахівці A-green на основі досвіду наших клієнтів.";
        $post = BlogPost::create($attributes + ['is_active' => true]);
        $post->translations()->create($translation + [
            'language_id' => $this->ukId,
            'title'       => $title,
            'slug'        => Str::slug($title) . '-' . $post->id,
            'excerpt'     => Str::limit($text, 160),
            'body'        => '<p>' . e($text) . '</p><h2>Головне</h2><p>Правильний вибір матеріалу економить час і гроші. Звертайтесь до менеджерів A-green — допоможемо підібрати рішення під ваше завдання.</p>',
        ]);
    }

    private function promotions(): void
    {
        $brand = fn(string $slug) => Brand::whereHas('translations', fn($q) => $q->where('slug', $slug))->value('id');
        $cat   = fn(string $slug) => Category::whereHas('translations', fn($q) => $q->where('slug', $slug))->value('id');
        $list = [
            ['Тиждень полірування: −12% на пасти Menzerna', $brand('menzerna'), $cat('poliruvaline-obladnannia'), 12, 20],
            ['−15% на респіратори та маски', null, $cat('masky-ta-respiratory'), 15, 35],
            ['Гігієна для офісу: −8% на продукцію Tork', $brand('tork'), null, 8, 50],
            ['Клеї Loctite: другий тюбик за півціни', $brand('loctite'), null, null, 25],
        ];
        $sort = Promotion::max('sort_order') ?? 0;
        foreach ($list as [$title, $brandId, $catId, $pct, $days]) {
            $promo = Promotion::create([
                'brand_id' => $brandId, 'category_id' => $catId, 'discount_percent' => $pct,
                'starts_at' => now()->subDays(5)->toDateString(), 'ends_at' => now()->addDays($days)->toDateString(),
                'sort_order' => ++$sort, 'is_active' => true,
            ]);
            $promo->translations()->create([
                'language_id' => $this->ukId, 'title' => $title, 'slug' => Str::slug($title) . '-' . $promo->id,
                'description' => '<p>' . e($title) . '. Пропозиція діє до вказаної дати або до закінчення товару на складі.</p>',
            ]);
        }
    }

    private function vacancies(): void
    {
        $list = [
            ['Менеджер з продажу (Львів)', 'Відділ продажу', 'Львів', 'Повна зайнятість'],
            ['Водій-експедитор', 'Логістика', 'Київ', 'Повна зайнятість'],
            ['Тренер навчального центру', 'Навчальний центр', 'Київ', 'Повна зайнятість'],
            ['Бухгалтер', 'Бухгалтерія', 'Київ', 'Часткова зайнятість'],
        ];
        $sort = Vacancy::max('sort_order') ?? 0;
        foreach ($list as [$title, $department, $city, $employment]) {
            $vacancy = Vacancy::create(['sort_order' => ++$sort, 'is_active' => true]);
            $vacancy->translations()->create([
                'language_id' => $this->ukId, 'title' => $title, 'department' => $department, 'city' => $city, 'employment' => $employment,
                'description' => "Шукаємо в команду A-green: {$title}. Офіційне працевлаштування, навчання, конкурентна зарплата.",
                'requirements' => ['Досвід роботи від 1 року', 'Відповідальність', 'Бажання розвиватися'],
            ]);
        }
    }

    private function testimonials(): void
    {
        $sort = Testimonial::max('sort_order') ?? 0;
        foreach ([
            ['ПП «Детейлінг Центр»', 'Замовляємо полірувальні пасти щомісяця — завжди в наявності й швидка доставка.', 5],
            ['ТОВ «Клінінг Груп»', 'Перевели всі обʼєкти на диспенсери Tork через A-green, економія відчутна.', 5],
            ['ТОВ «Кузов Майстер»', 'Хороший асортимент абразивів, менеджер допоміг підібрати зернистість.', 4],
            ['ТОВ «Метал-Сервіс»', 'Зручний кабінет: документи й рахунки завжди під рукою.', 5],
        ] as [$author, $text, $rating]) {
            Testimonial::create(['author' => $author, 'text' => $text, 'rating' => $rating, 'sort_order' => ++$sort, 'is_active' => true]);
        }
    }

    /** @return array<string, User> */
    private function users(): array
    {
        $users = [];
        $make = function (string $email, string $role, array $extra = []) use (&$users) {
            $user = new User();
            $user->forceFill($extra + [
                'name'              => $this->pick(self::FIRST_NAMES),
                'last_name'         => $this->pick(self::LAST_NAMES),
                'email'             => $email,
                'role'              => $role,
                'password'          => Hash::make(self::PASSWORD),
                'email_verified_at' => now()->subDays(mt_rand(1, 300)),
                'last_login_at'     => now()->subDays(mt_rand(0, 30)),
            ])->save();
            $user->forceFill(['created_at' => now()->subDays(mt_rand(5, 330))])->saveQuietly();

            return $users[$email] = $user;
        };

        $make('admin@demo.test', 'admin', ['name' => 'Адмін', 'last_name' => 'Демо']);
        $make('manager@demo.test', 'manager', ['name' => 'Марина', 'last_name' => 'Менеджер']);
        $make('content@demo.test', 'content', ['name' => 'Катерина', 'last_name' => 'Контент']);

        $np = ShippingProvider::where('driver', 'nova_poshta')->value('id');
        $make('client@demo.test', 'client', [
            'name' => 'Іван', 'last_name' => 'Іваненко', 'phone' => '+380671110001', 'company' => 'ФОП Іваненко',
            'default_shipping_provider_id' => $np, 'default_city' => 'Київ', 'default_address' => 'Відділення №12',
        ]);
        $make('business@demo.test', 'business_client', ['name' => 'Олена', 'last_name' => 'Бізнес', 'phone' => '+380671110002', 'company' => 'ТОВ «Автосервіс Плюс»', 'company_code' => '41234567']);
        $make('partner@demo.test', 'business_partner', ['name' => 'Роман', 'last_name' => 'Партнер', 'phone' => '+380671110003', 'company' => 'ТОВ «ProPaint»', 'company_code' => '39876543']);

        for ($i = 1; $i <= 36; $i++) {
            $request = $i <= 8 ? $this->pick(User::BUSINESS_ROLES) : null;
            $make("customer{$i}@demo.test", $this->chance(15) ? $this->pick(User::BUSINESS_ROLES) : 'client', [
                'phone'             => sprintf('+38067%07d', 2000000 + $i),
                'company'           => $request || $this->chance(40) ? $this->pick(self::COMPANIES) : null,
                'requested_role'    => $request,
                'role_requested_at' => $request ? now()->subDays(mt_rand(0, 10)) : null,
                'email_verified_at' => $this->chance(90) ? now()->subDays(mt_rand(1, 300)) : null,
            ]);
        }

        return $users;
    }

    private function orders(array $users): void
    {
        $statuses = OrderStatus::pluck('id', 'key');
        $shipping = ShippingProvider::available()->pluck('id')->all();
        $payment  = PaymentProvider::where('is_active', true)->pluck('id')->all();
        $products = Product::where('is_active', true)->with('translations')->get();

        $customers = collect($users)->reject(fn(User $u) => $u->isStaff())->values();
        $plan = [];
        for ($i = 0; $i < 24; $i++) {
            $plan[] = $users['client@demo.test'];
        }
        foreach (['business@demo.test', 'partner@demo.test'] as $email) {
            for ($i = 0; $i < 8; $i++) {
                $plan[] = $users[$email];
            }
        }
        for ($i = 0; $i < 40; $i++) {
            $plan[] = $this->chance(70) ? $customers->random() : null; // null — гість
        }

        foreach ($plan as $k => $user) {
            $date = now()->subDays(mt_rand(0, 300))->setTime(mt_rand(8, 20), mt_rand(0, 59));
            $age  = $date->diffInDays(now());
            $key  = match (true) {
                $age > 30 => $this->chance(85) ? 'completed' : 'cancelled',
                $age > 10 => $this->pick(['delivered', 'completed', 'shipped']),
                default   => $this->pick(['pending', 'confirmed', 'awaiting_payment', 'processing', 'shipped']),
            };
            $items = $products->random(mt_rand(1, 5));
            $lines = $items->map(function (Product $p) {
                $qty = mt_rand(1, 3);

                return ['product' => $p, 'qty' => $qty, 'total' => (float) $p->price * $qty];
            });

            $order = Order::create([
                'user_id'              => $user?->id,
                'number'               => 'tmp-' . Str::uuid(),
                'type'                 => 'regular',
                'order_status_id'      => $statuses['pending'],
                'customer_name'        => $user ? $user->fullName() : $this->pick(self::FIRST_NAMES) . ' ' . $this->pick(self::LAST_NAMES),
                'customer_phone'       => $user?->phone ?? sprintf('+38050%07d', mt_rand(1000000, 9999999)),
                'customer_email'       => $user?->email ?? 'guest' . $k . '@demo.test',
                'customer_company'     => $user?->company,
                'shipping_provider_id' => $this->pick($shipping),
                'delivery_city'        => $this->pick(self::CITIES),
                'delivery_address'     => 'Відділення №' . mt_rand(1, 120),
                'tracking_number'      => in_array($key, ['shipped', 'delivered', 'completed'], true) ? '2045' . mt_rand(1000000000, 9999999999) : null,
                'payment_provider_id'  => $this->pick($payment),
                'payment_status'       => in_array($key, ['completed', 'delivered', 'shipped'], true) ? 'paid' : 'pending',
                'total'                => round($lines->sum('total'), 2),
                'locale'               => 'uk',
            ]);
            $order->forceFill(['number' => Order::makeNumber($order->id), 'created_at' => $date, 'updated_at' => $date])->saveQuietly();
            $order->items()->createMany($lines->map(fn($l) => [
                'product_id' => $l['product']->id,
                'sku'        => $l['product']->sku,
                'name'       => $l['product']->translations->firstWhere('language_id', $this->ukId)?->name ?? $l['product']->sku,
                'price'      => $l['product']->price,
                'quantity'   => $l['qty'],
                'total'      => $l['total'],
            ])->all());

            // Зміна статусу через save — для «Виконано» спрацює нарахування кешбеку
            if ($key !== 'pending') {
                $order->update(['order_status_id' => $statuses[$key]]);
                CashbackTransaction::where('order_id', $order->id)->update(['created_at' => $date->copy()->addDays(5)]);
            }
        }
    }

    private function clientExtras(User $client): void
    {
        $orders = $client->orders()->latest('id')->get();
        $disk   = Storage::disk(CustomerDocument::DISK);
        $pdf    = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF";

        $docs = [];
        foreach ($orders->take(6) as $order) {
            $docs[] = ['invoice', 'Рахунок-фактура №' . $order->number, $order];
            if ($this->chance(50)) {
                $docs[] = ['waybill', 'Видаткова накладна №' . $order->number, $order];
            }
        }
        $docs[] = ['act', 'Акт звірки за I квартал 2026', null];
        $docs[] = ['act', 'Акт виконаних робіт №04', null];
        $docs[] = ['contract', 'Договір поставки №2025-001', null];
        $docs[] = ['other', 'Сертифікат відповідності продукції', null];

        foreach ($docs as $i => [$type, $title, $order]) {
            $path = "customer-documents/demo-{$client->id}-{$i}.pdf";
            $disk->put($path, $pdf . str_repeat(' ', mt_rand(20000, 200000)));
            CustomerDocument::create([
                'user_id'   => $client->id,
                'order_id'  => $order?->id,
                'type'      => $type,
                'title'     => $title,
                'path'      => $path,
                'issued_at' => $order?->created_at ?? now()->subDays(mt_rand(10, 200)),
            ]);
        }

        CashbackTransaction::create([
            'user_id' => $client->id, 'amount' => -150, 'description' => 'Списано при оплаті замовлення',
        ]);

        foreach ([
            ['Консультація', 'Підкажіть аналог ґрунтовки Novol 1K для пластику.', 'done'],
            ['Сервіс', 'Диспенсер Tork не подає рушники, потрібен ремонт.', 'in_progress'],
            ['Виклик спеціаліста', 'Потрібен монтаж 6 дозаторів у новому офісі.', 'new'],
            ['Консультація', 'Чи є в наявності респіратори FFP3 на 200 шт?', 'done'],
        ] as $i => [$kind, $message, $status]) {
            $lead = Lead::create([
                'type' => 'support', 'status' => $status, 'user_id' => $client->id,
                'name' => $client->fullName(), 'phone' => $client->phone, 'email' => $client->email,
                'message' => $message, 'payload' => ['request' => $kind],
            ]);
            $lead->forceFill(['created_at' => now()->subDays(40 - $i * 10)])->saveQuietly();
        }
    }

    private function leads(array $users): void
    {
        $products = Product::where('is_active', true)->pluck('id')->all();
        $messages = [
            'contact'       => ['Зателефонуйте, будь ласка, щодо оптових цін.', 'Чи можлива доставка в суботу?', 'Потрібна консультація по полірувальних машинах.'],
            'quick_order'   => [null],
            'stock_notify'  => [null],
            'price_request' => ['Цікавить ціна від 50 шт.', 'Яка ціна при щомісячних закупівлях?'],
            'partner'       => ['Хочемо стати дилером у нашому регіоні.', 'Пропонуємо співпрацю: мережа з 5 СТО.'],
            'service'       => ['Потрібне обслуговування фарбувальної камери.', 'Цікавить навчання персоналу.'],
            'vacancy'       => ['Маю досвід 3 роки у продажах B2B.'],
        ];

        for ($i = 0; $i < 45; $i++) {
            $type   = $this->pick(array_keys($messages));
            $status = $i < 12 ? 'new' : $this->pick(['new', 'in_progress', 'done', 'done', 'spam']);
            $lead = Lead::create([
                'type'       => $type,
                'status'     => $status,
                'product_id' => in_array($type, ['quick_order', 'stock_notify', 'price_request'], true) ? $this->pick($products) : null,
                'name'       => $this->pick(self::FIRST_NAMES) . ' ' . $this->pick(self::LAST_NAMES),
                'phone'      => sprintf('+38063%07d', mt_rand(1000000, 9999999)),
                'email'      => $this->chance(60) ? 'lead' . $i . '@demo.test' : null,
                'company'    => $this->chance(40) ? $this->pick(self::COMPANIES) : null,
                'message'    => $this->pick($messages[$type]),
                'payload'    => match ($type) {
                    'quick_order', 'price_request' => ['quantity' => mt_rand(1, 100)],
                    'partner' => ['partner_type' => $this->pick(['Дилер', 'Оптовий покупець', 'Партнер по установці'])],
                    'service' => ['service' => $this->pick(['Технічна підтримка', 'Навчальний центр', 'Обслуговування'])],
                    'vacancy' => ['vacancy' => 'Менеджер з продажу'],
                    default   => null,
                },
                'manager_id'   => $status !== 'new' ? $users['manager@demo.test']->id : null,
                'processed_at' => in_array($status, ['done', 'spam'], true) ? now()->subDays(mt_rand(0, 20)) : null,
            ]);
            $lead->forceFill(['created_at' => now()->subDays(mt_rand(0, 60))->subMinutes(mt_rand(0, 600))])->saveQuietly();
        }
    }
}
