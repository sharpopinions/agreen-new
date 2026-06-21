<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\BrandTranslation;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['uk' => ['name' => 'Kimberly-Clark', 'slug' => 'kimberly-clark', 'description' => 'Американський виробник засобів гігієни та витратних матеріалів']],
            ['uk' => ['name' => 'Mirka',           'slug' => 'mirka',           'description' => 'Фінський виробник абразивних матеріалів та інструментів']],
            ['uk' => ['name' => 'DuPont',          'slug' => 'dupont',          'description' => 'Американський виробник засобів індивідуального захисту']],
            ['uk' => ['name' => '3M',              'slug' => '3m',              'description' => 'Американська компанія, виробник промислових матеріалів']],
            ['uk' => ['name' => 'Sika',            'slug' => 'sika',            'description' => 'Швейцарський виробник клеїв, герметиків та покриттів']],
            ['uk' => ['name' => 'Dettol',          'slug' => 'dettol',          'description' => 'Британський бренд засобів дезінфекції та гігієни']],
            ['uk' => ['name' => 'Novol',           'slug' => 'novol',           'description' => 'Польський виробник лакофарбових матеріалів']],
            ['uk' => ['name' => 'Motip',           'slug' => 'motip',           'description' => 'Нідерландський бренд автохімії']],
        ];

        foreach ($brands as $sort => $data) {
            $brand = Brand::create([
                'store_id'   => 1,
                'sort_order' => $sort,
                'is_active'  => true,
            ]);

            BrandTranslation::create([
                'brand_id'    => $brand->id,
                'language_id' => 1,
                'name'        => $data['uk']['name'],
                'slug'        => $data['uk']['slug'],
                'description' => $data['uk']['description'],
            ]);

            BrandTranslation::create([
                'brand_id'    => $brand->id,
                'language_id' => 2,
                'name'        => $data['uk']['name'],
                'slug'        => $data['uk']['slug'],
                'description' => $data['uk']['description'],
            ]);
        }
    }
}
