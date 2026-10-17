<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Language;
use App\Models\Promotion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Демо-акції з Claude Design (extra-pages4.jsx → PROMOS) під наявні бренди.
 * Ідемпотентний: пропускає акцію з таким самим заголовком.
 */
class PromotionSeeder extends Seeder
{
    private const PROMOS = [
        // brand slug, category slug, %, днів до кінця, заголовок, опис
        ['kimberly-clark', 'dozuiuche-obladnannia', 10, 45, '−10% на дозуюче обладнання Kimberly-Clark',
            'Спеціальна пропозиція на лінійку дозуючого обладнання — диспенсери для рушників, мила, серветок та туалетного паперу. Знижка діє при замовленні від 1 шт.'],
        ['mirka', 'abrazyvni-materialy', 15, 60, '−15% на абразивні диски Mirka серії Abranet',
            'Абразивні диски Mirka Abranet з сітчастою основою для безпилового шліфування за спеціальною ціною.'],
        ['3m', null, null, 45, 'Безкоштовна доставка від 2 000 ₴ на товари 3M',
            'Замовляйте продукцію 3M на суму від 2 000 ₴ — доставку по Україні оплачуємо ми.'],
        ['dupont', 'zakhysni-zasoby', 20, 30, '−20% на захисні комбінезони DuPont Tyvek',
            'Захисні комбінезони DuPont Tyvek для фарбувальних камер і виробництва зі знижкою 20%.'],
        ['sika', 'klei-ta-hermetyky', 10, 75, '−10% на клеї та герметики Sika при замовленні від 5 шт',
            'Клеї та герметики Sika для кузовного ремонту — знижка 10% при замовленні від 5 одиниць.'],
        ['dettol', 'hihiienichna-produktsiia', 5, 45, '−5% на гігієнічну продукцію Dettol Professional',
            'Професійні засоби гігієни Dettol для підприємств і сервісних центрів.'],
    ];

    public function run(): void
    {
        $uk = Language::where('code', 'uk')->value('id');
        if (! $uk) {
            return;
        }
        $id = fn($model, ?string $slug) => $slug
            ? $model::whereHas('translations', fn($q) => $q->where('slug', $slug))->value('id')
            : null;

        foreach (self::PROMOS as $i => [$brand, $category, $pct, $days, $title, $text]) {
            $exists = Promotion::whereHas('translations', fn($q) => $q->where('title', $title))->exists();
            if ($exists) {
                continue;
            }

            $promo = Promotion::create([
                'brand_id'         => $id(Brand::class, $brand),
                'category_id'      => $id(Category::class, $category),
                'discount_percent' => $pct,
                'ends_at'          => now()->addDays($days)->toDateString(),
                'sort_order'       => $i,
                'is_active'        => true,
            ]);
            $promo->translations()->create([
                'language_id' => $uk,
                'title'       => $title,
                'slug'        => Str::slug(Str::before($title, ' при ')),
                'description' => '<p>' . e($text) . '</p>',
            ]);
        }
    }
}
