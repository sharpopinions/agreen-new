<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Brand;
use App\Models\BrandTranslation;
use App\Models\Badge;
use App\Models\BadgeTranslation;
use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Завантажуємо категорії за slug (uk)
        $catSlug = fn(string $slug) => CategoryTranslation::where('slug', $slug)->where('language_id', 1)->firstOrFail()->category_id;

        // Завантажуємо бренди за slug
        $brandId = fn(string $slug) => BrandTranslation::where('slug', $slug)->where('language_id', 1)->firstOrFail()->brand_id;

        // Завантажуємо бейджи за назвою
        $badgeId = fn(string $name) => BadgeTranslation::where('name', $name)->where('language_id', 1)->firstOrFail()->badge_id;

        $products = [
            [
                'brand'          => 'kimberly-clark',
                'stock_status_id'=> 1,
                'sku'            => 'SE-50281',
                'price'          => 1099.00,
                'old_price'      => null,
                'rating'         => 4.2,
                'reviews_count'  => 12,
                'stock_quantity' => 150,
                'categories'     => ['vytratni-materialy'],
                'badges'         => ['Хіт'],
                'uk' => [
                    'name'        => 'Диспенсер паперових рушників Kimberly-Clark 9960',
                    'slug'        => 'dyspenser-paperovykh-rushnykiv-kimberly-clark-9960',
                    'description' => 'Диспенсер для паперових рушників серії 9960. Надійна конструкція з ABS-пластику, ємність до 400 листів.',
                ],
                'en' => [
                    'name'        => 'Kimberly-Clark 9960 Paper Towel Dispenser',
                    'slug'        => 'kimberly-clark-9960-paper-towel-dispenser',
                    'description' => 'Paper towel dispenser for the 9960 series. Durable ABS plastic construction, capacity up to 400 sheets.',
                ],
            ],
            [
                'brand'          => '3m',
                'stock_status_id'=> 1,
                'sku'            => 'AX-001',
                'price'          => 450.00,
                'old_price'      => 580.00,
                'rating'         => 4.8,
                'reviews_count'  => 34,
                'stock_quantity' => 43,
                'categories'     => ['dozuiuche-obladnannia'],
                'badges'         => ['Акція'],
                'uk' => [
                    'name'        => 'Активатор системи дозування X Pro 5л',
                    'slug'        => 'aktyvator-systemy-dozuvannia-x-pro-5l',
                    'description' => 'Активатор для систем дозування X Pro. Об\'єм 5 літрів. Підходить для промислових диспенсерів серії Pro.',
                ],
                'en' => [
                    'name'        => 'X Pro Dosing System Activator 5L',
                    'slug'        => 'x-pro-dosing-system-activator-5l',
                    'description' => 'Activator for X Pro dosing systems. Volume 5 litres. Compatible with Pro series industrial dispensers.',
                ],
            ],
            [
                'brand'          => 'mirka',
                'stock_status_id'=> 1,
                'sku'            => 'MA-120',
                'price'          => 89.00,
                'old_price'      => null,
                'rating'         => 4.5,
                'reviews_count'  => 67,
                'stock_quantity' => 280,
                'categories'     => ['abrazyvni-materialy'],
                'badges'         => [],
                'uk' => [
                    'name'        => 'Абразивний диск Mirka Abranet P120 150мм',
                    'slug'        => 'abrazyvnyi-dysk-mirka-abranet-p120-150mm',
                    'description' => 'Шліфувальний диск Mirka Abranet діаметром 150 мм з зернистістю P120. Сітчаста структура забезпечує ефективний пиловідвід.',
                ],
                'en' => [
                    'name'        => 'Mirka Abranet P120 Sanding Disc 150mm',
                    'slug'        => 'mirka-abranet-p120-sanding-disc-150mm',
                    'description' => 'Mirka Abranet 150mm sanding disc with P120 grit. Mesh structure ensures efficient dust extraction.',
                ],
            ],
            [
                'brand'          => 'dupont',
                'stock_status_id'=> 1,
                'sku'            => 'DT-400',
                'price'          => 320.00,
                'old_price'      => null,
                'rating'         => 4.1,
                'reviews_count'  => 23,
                'stock_quantity' => 62,
                'categories'     => ['zakhysni-zasoby'],
                'badges'         => [],
                'uk' => [
                    'name'        => 'Захисний комбінезон DuPont Tyvek 400 XL',
                    'slug'        => 'zakhysnyi-kombineson-dupont-tyvek-400-xl',
                    'description' => 'Одноразовий захисний комбінезон DuPont Tyvek 400 розміру XL. Захист від часточок і бризок хімічних речовин.',
                ],
                'en' => [
                    'name'        => 'DuPont Tyvek 400 XL Protective Coverall',
                    'slug'        => 'dupont-tyvek-400-xl-protective-coverall',
                    'description' => 'Disposable DuPont Tyvek 400 protective coverall in XL size. Protection against particles and chemical splashes.',
                ],
            ],
            [
                'brand'          => '3m',
                'stock_status_id'=> 1,
                'sku'            => '3M-P3',
                'price'          => 680.00,
                'old_price'      => 820.00,
                'rating'         => 4.9,
                'reviews_count'  => 89,
                'stock_quantity' => 15,
                'categories'     => ['poliruvaline-obladnannia'],
                'badges'         => ['Акція'],
                'uk' => [
                    'name'        => 'Поліроль 3M Perfect-It III 1000мл',
                    'slug'        => 'polirol-3m-perfect-it-iii-1000ml',
                    'description' => 'Фінішна поліроль 3M Perfect-It III обсягом 1000 мл. Усуває дрібні подряпини та надає глибокий блиск.',
                ],
                'en' => [
                    'name'        => '3M Perfect-It III Polish 1000ml',
                    'slug'        => '3m-perfect-it-iii-polish-1000ml',
                    'description' => '3M Perfect-It III finish polish 1000ml. Removes fine scratches and provides deep gloss.',
                ],
            ],
            [
                'brand'          => 'sika',
                'stock_status_id'=> 1,
                'sku'            => 'SF-221',
                'price'          => 520.00,
                'old_price'      => null,
                'rating'         => 4.3,
                'reviews_count'  => 15,
                'stock_quantity' => 78,
                'categories'     => ['klei-ta-hermetyky'],
                'badges'         => [],
                'uk' => [
                    'name'        => 'Клей-герметик Sikaflex-221 чорний 300мл',
                    'slug'        => 'klei-hermetyk-sikaflex-221-chornyi-300ml',
                    'description' => 'Однокомпонентний поліуретановий клей-герметик Sikaflex-221. Чорний колір, 300 мл. Адгезія до більшості матеріалів.',
                ],
                'en' => [
                    'name'        => 'Sikaflex-221 Black Adhesive Sealant 300ml',
                    'slug'        => 'sikaflex-221-black-adhesive-sealant-300ml',
                    'description' => 'One-component polyurethane adhesive sealant Sikaflex-221. Black, 300ml. Adhesion to most materials.',
                ],
            ],
            [
                'brand'          => 'dettol',
                'stock_status_id'=> 1,
                'sku'            => 'DT-5L',
                'price'          => 290.00,
                'old_price'      => null,
                'rating'         => 4.6,
                'reviews_count'  => 45,
                'stock_quantity' => 120,
                'categories'     => ['hihiienichna-produktsiia'],
                'badges'         => ['Новинка'],
                'uk' => [
                    'name'        => 'Рідке мило антибактеріальне Dettol 5л',
                    'slug'        => 'ridke-mylo-antybakterialne-dettol-5l',
                    'description' => 'Антибактеріальне рідке мило Dettol у форматі 5 літрів. Знищує 99,9% бактерій. Підходить для диспенсерів.',
                ],
                'en' => [
                    'name'        => 'Dettol Antibacterial Liquid Soap 5L',
                    'slug'        => 'dettol-antibacterial-liquid-soap-5l',
                    'description' => 'Dettol antibacterial liquid soap in 5 litre format. Kills 99.9% of bacteria. Compatible with dispensers.',
                ],
            ],
            [
                'brand'          => 'novol',
                'stock_status_id'=> 1,
                'sku'            => 'VP-08',
                'price'          => 185.00,
                'old_price'      => null,
                'rating'         => 4.0,
                'reviews_count'  => 8,
                'stock_quantity' => 34,
                'categories'     => ['lakofarbovi-materialy'],
                'badges'         => [],
                'uk' => [
                    'name'        => 'Ґрунтовка епоксидна Novol П-ЕФ 0.8кг',
                    'slug'        => 'hruntovka-epoksydna-novol-p-ef-08kh',
                    'description' => 'Двокомпонентна епоксидна ґрунтовка Novol П-ЕФ. Маса 0.8 кг. Відмінна адгезія до металу та склопластику.',
                ],
                'en' => [
                    'name'        => 'Novol P-EF Epoxy Primer 0.8kg',
                    'slug'        => 'novol-p-ef-epoxy-primer-0-8kg',
                    'description' => 'Two-component Novol P-EF epoxy primer. Weight 0.8kg. Excellent adhesion to metal and fibreglass.',
                ],
            ],
            [
                'brand'          => 'novol',
                'stock_status_id'=> 1,
                'sku'            => 'NOV-GRUN-001',
                'price'          => 320.00,
                'old_price'      => null,
                'rating'         => 4.3,
                'reviews_count'  => 19,
                'stock_quantity' => 56,
                'categories'     => ['lakofarbovi-materialy'],
                'badges'         => ['Новинка'],
                'uk' => [
                    'name'        => 'Ґрунт акриловий Novol 1K',
                    'slug'        => 'grunt-acrylovy-novol-1k',
                    'description' => 'Однокомпонентний акриловий ґрунт для підготовки поверхні.',
                ],
                'en' => [
                    'name'        => 'Novol 1K Acrylic Primer',
                    'slug'        => 'novol-1k-acrylic-primer',
                    'description' => 'One-component acrylic primer for surface preparation.',
                ],
            ],
            [
                'brand'          => 'motip',
                'stock_status_id'=> 1,
                'sku'            => 'MOT-WAX-001',
                'price'          => 185.00,
                'old_price'      => 220.00,
                'rating'         => 4.4,
                'reviews_count'  => 31,
                'stock_quantity' => 88,
                'categories'     => ['poliruvaline-obladnannia'],
                'badges'         => ['Акція'],
                'uk' => [
                    'name'        => 'Поліроль захисний Motip Wax',
                    'slug'        => 'polirol-zakhysnyi-motip-wax',
                    'description' => 'Захисний поліроль з карнаубським воском.',
                ],
                'en' => [
                    'name'        => 'Motip Wax Protective Polish',
                    'slug'        => 'motip-wax-protective-polish',
                    'description' => 'Protective polish with carnauba wax.',
                ],
            ],
            [
                'brand'          => 'mirka',
                'stock_status_id'=> 1,
                'sku'            => 'MIR-SAND-080',
                'price'          => 95.00,
                'old_price'      => null,
                'rating'         => 4.6,
                'reviews_count'  => 52,
                'stock_quantity' => 210,
                'categories'     => ['abrazyvni-materialy'],
                'badges'         => ['Хіт'],
                'uk' => [
                    'name'        => 'Абразивний папір Mirka P80',
                    'slug'        => 'abrazyvnyi-papir-mirka-p80',
                    'description' => 'Шліфувальний папір зернистістю P80.',
                ],
                'en' => [
                    'name'        => 'Mirka Sandpaper P80',
                    'slug'        => 'mirka-sandpaper-p80',
                    'description' => 'Sandpaper with P80 grit.',
                ],
            ],
            [
                'brand'          => 'novol',
                'stock_status_id'=> 2,
                'sku'            => 'NOV-LAK-001',
                'price'          => 540.00,
                'old_price'      => null,
                'rating'         => 4.7,
                'reviews_count'  => 14,
                'stock_quantity' => null,
                'categories'     => ['lakofarbovi-materialy'],
                'badges'         => [],
                'uk' => [
                    'name'        => 'Лак акриловий Novol 2K',
                    'slug'        => 'lak-acrylovy-novol-2k',
                    'description' => 'Двокомпонентний акриловий лак високого блиску.',
                ],
                'en' => [
                    'name'        => 'Novol 2K Acrylic Varnish',
                    'slug'        => 'novol-2k-acrylic-varnish',
                    'description' => 'Two-component high-gloss acrylic varnish.',
                ],
            ],
        ];

        foreach ($products as $sort => $data) {
            $product = Product::create([
                'store_id'        => 1,
                'brand_id'        => $brandId($data['brand']),
                'stock_status_id' => $data['stock_status_id'],
                'sku'             => $data['sku'],
                'price'           => $data['price'],
                'old_price'       => $data['old_price'],
                'rating'          => $data['rating'],
                'reviews_count'   => $data['reviews_count'],
                'stock_quantity'  => $data['stock_quantity'],
                'sort_order'      => $sort,
                'is_active'       => true,
            ]);

            ProductTranslation::create([
                'product_id'  => $product->id,
                'language_id' => 1,
                'name'        => $data['uk']['name'],
                'slug'        => $data['uk']['slug'],
                'description' => $data['uk']['description'],
            ]);

            ProductTranslation::create([
                'product_id'  => $product->id,
                'language_id' => 2,
                'name'        => $data['en']['name'],
                'slug'        => $data['en']['slug'],
                'description' => $data['en']['description'],
            ]);

            $categoryIds = array_map(fn($s) => $catSlug($s), $data['categories']);
            $product->categories()->attach($categoryIds);

            foreach ($data['badges'] as $badgeName) {
                $product->badges()->attach($badgeId($badgeName));
            }
        }
    }
}
