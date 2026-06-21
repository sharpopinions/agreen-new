<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\BadgeTranslation;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'color'    => '#ffffff',
                'bg_color' => '#3b82f6',
                'uk'       => 'Новинка',
                'en'       => 'New',
            ],
            [
                'color'    => '#ffffff',
                'bg_color' => '#ef4444',
                'uk'       => 'Акція',
                'en'       => 'Sale',
            ],
            [
                'color'    => '#ffffff',
                'bg_color' => '#f59e0b',
                'uk'       => 'Хіт',
                'en'       => 'Hit',
            ],
        ];

        foreach ($badges as $index => $data) {
            $badge = Badge::create([
                'store_id'   => 1,
                'color'      => $data['color'],
                'bg_color'   => $data['bg_color'],
                'sort_order' => $index,
                'is_active'  => true,
            ]);

            BadgeTranslation::create([
                'badge_id'    => $badge->id,
                'language_id' => 1,
                'name'        => $data['uk'],
            ]);

            BadgeTranslation::create([
                'badge_id'    => $badge->id,
                'language_id' => 2,
                'name'        => $data['en'],
            ]);
        }
    }
}
