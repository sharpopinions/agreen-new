<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'uk' => ['name' => 'Витратні матеріали',     'slug' => 'vytratni-materialy'],
                'en' => ['name' => 'Consumables',            'slug' => 'consumables'],
                'children' => [
                    ['uk' => ['name' => 'Ганчірки та серветки',    'slug' => 'hanchirky-ta-servetky'],    'en' => ['name' => 'Rags & Wipes',        'slug' => 'rags-wipes']],
                    ['uk' => ['name' => 'Засоби для прибирання',   'slug' => 'zasoby-dlia-prybyrannia'],  'en' => ['name' => 'Cleaning Products',   'slug' => 'cleaning-products']],
                    ['uk' => ['name' => 'Паперові рушники',        'slug' => 'paperovi-rushnyky'],        'en' => ['name' => 'Paper Towels',        'slug' => 'paper-towels']],
                ],
            ],
            [
                'uk' => ['name' => 'Абразивні матеріали',    'slug' => 'abrazyvni-materialy'],
                'en' => ['name' => 'Abrasives',              'slug' => 'abrasives'],
                'children' => [
                    ['uk' => ['name' => 'Шліфувальні диски',       'slug' => 'shlifuvalni-dysky'],        'en' => ['name' => 'Sanding Discs',       'slug' => 'sanding-discs']],
                    ['uk' => ['name' => 'Абразивні круги',         'slug' => 'abrazyvni-kruhy'],          'en' => ['name' => 'Abrasive Wheels',     'slug' => 'abrasive-wheels']],
                    ['uk' => ['name' => 'Шліфувальна шкурка',      'slug' => 'shlifuvalna-shkurka'],      'en' => ['name' => 'Sandpaper',           'slug' => 'sandpaper']],
                ],
            ],
            [
                'uk' => ['name' => 'Дозуюче обладнання',    'slug' => 'dozuiuche-obladnannia'],
                'en' => ['name' => 'Dispensing Equipment',  'slug' => 'dispensing-equipment'],
                'children' => [
                    ['uk' => ['name' => 'Диспенсери',              'slug' => 'dyspensery'],               'en' => ['name' => 'Dispensers',          'slug' => 'dispensers']],
                    ['uk' => ['name' => 'Дозатори рідини',         'slug' => 'dozatory-ridyny'],          'en' => ['name' => 'Liquid Dispensers',   'slug' => 'liquid-dispensers']],
                    ['uk' => ['name' => 'Автоматичні системи',     'slug' => 'avtomatychni-systemy'],     'en' => ['name' => 'Automatic Systems',   'slug' => 'automatic-systems']],
                ],
            ],
            [
                'uk' => ['name' => 'Лакофарбові матеріали', 'slug' => 'lakofarbovi-materialy'],
                'en' => ['name' => 'Paints & Coatings',     'slug' => 'paints-coatings'],
                'children' => [
                    ['uk' => ['name' => 'Ґрунтовки',              'slug' => 'hruntovky'],                'en' => ['name' => 'Primers',             'slug' => 'primers']],
                    ['uk' => ['name' => 'Автолаки',               'slug' => 'avtolaky'],                 'en' => ['name' => 'Auto Lacquers',       'slug' => 'auto-lacquers']],
                    ['uk' => ['name' => 'Фарби',                  'slug' => 'farby'],                    'en' => ['name' => 'Paints',              'slug' => 'paints']],
                ],
            ],
            [
                'uk' => ['name' => 'Гігієнічна продукція',  'slug' => 'hihiienichna-produktsiia'],
                'en' => ['name' => 'Hygiene Products',      'slug' => 'hygiene-products'],
                'children' => [
                    ['uk' => ['name' => 'Мило та антисептики',     'slug' => 'mylo-ta-antyseptyki'],      'en' => ['name' => 'Soap & Antiseptics',  'slug' => 'soap-antiseptics']],
                    ['uk' => ['name' => 'Гігієнічні рушники',      'slug' => 'hihiienichni-rushnyky'],    'en' => ['name' => 'Hygiene Towels',      'slug' => 'hygiene-towels']],
                    ['uk' => ['name' => 'Туалетний папір',         'slug' => 'tualetnyi-papir'],          'en' => ['name' => 'Toilet Paper',        'slug' => 'toilet-paper']],
                ],
            ],
            [
                'uk' => ['name' => 'Захисні засоби',        'slug' => 'zakhysni-zasoby'],
                'en' => ['name' => 'Protective Equipment',  'slug' => 'protective-equipment'],
                'children' => [
                    ['uk' => ['name' => 'Рукавиці захисні',        'slug' => 'rukavytsi-zakhysni'],       'en' => ['name' => 'Protective Gloves',   'slug' => 'protective-gloves']],
                    ['uk' => ['name' => 'Захисні комбінезони',      'slug' => 'zakhysni-kombinezony'],     'en' => ['name' => 'Protective Suits',    'slug' => 'protective-suits']],
                    ['uk' => ['name' => 'Маски та респіратори',     'slug' => 'masky-ta-respiratory'],     'en' => ['name' => 'Masks & Respirators', 'slug' => 'masks-respirators']],
                ],
            ],
            [
                'uk' => ['name' => 'Клеї та герметики',     'slug' => 'klei-ta-hermetyky'],
                'en' => ['name' => 'Adhesives & Sealants',  'slug' => 'adhesives-sealants'],
                'children' => [
                    ['uk' => ['name' => 'Конструкційні клеї',      'slug' => 'konstruktsiini-klei'],      'en' => ['name' => 'Structural Adhesives', 'slug' => 'structural-adhesives']],
                    ['uk' => ['name' => 'Силіконові герметики',     'slug' => 'sylikonovi-hermetyky'],     'en' => ['name' => 'Silicone Sealants',   'slug' => 'silicone-sealants']],
                    ['uk' => ['name' => 'Монтажна піна',           'slug' => 'montazhna-pina'],           'en' => ['name' => 'Mounting Foam',       'slug' => 'mounting-foam']],
                ],
            ],
            [
                'uk' => ['name' => 'Полірувальне обладнання', 'slug' => 'poliruvaline-obladnannia'],
                'en' => ['name' => 'Polishing Equipment',    'slug' => 'polishing-equipment'],
                'children' => [
                    ['uk' => ['name' => 'Полірувальні машини',      'slug' => 'poliruvaini-mashyny'],      'en' => ['name' => 'Polishing Machines',  'slug' => 'polishing-machines']],
                    ['uk' => ['name' => 'Полірувальні круги',       'slug' => 'poliruvaini-kruhy'],        'en' => ['name' => 'Polishing Pads',      'slug' => 'polishing-pads']],
                    ['uk' => ['name' => 'Паста для полірування',    'slug' => 'pasta-dlia-poluruvannia'],  'en' => ['name' => 'Polishing Compound',  'slug' => 'polishing-compound']],
                ],
            ],
        ];

        foreach ($categories as $sort => $data) {
            $parent = Category::create([
                'store_id'   => 1,
                'parent_id'  => null,
                'sort_order' => $sort,
                'is_active'  => true,
            ]);

            CategoryTranslation::create(['category_id' => $parent->id, 'language_id' => 1, 'name' => $data['uk']['name'], 'slug' => $data['uk']['slug'], 'description' => null]);
            CategoryTranslation::create(['category_id' => $parent->id, 'language_id' => 2, 'name' => $data['en']['name'], 'slug' => $data['en']['slug'], 'description' => null]);

            foreach ($data['children'] as $childSort => $child) {
                $sub = Category::create([
                    'store_id'   => 1,
                    'parent_id'  => $parent->id,
                    'sort_order' => $childSort,
                    'is_active'  => true,
                ]);

                CategoryTranslation::create(['category_id' => $sub->id, 'language_id' => 1, 'name' => $child['uk']['name'], 'slug' => $child['uk']['slug'], 'description' => null]);
                CategoryTranslation::create(['category_id' => $sub->id, 'language_id' => 2, 'name' => $child['en']['name'], 'slug' => $child['en']['slug'], 'description' => null]);
            }
        }
    }
}
