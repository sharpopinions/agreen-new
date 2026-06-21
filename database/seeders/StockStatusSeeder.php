<?php

namespace Database\Seeders;

use App\Models\StockStatus;
use App\Models\StockStatusTranslation;
use Illuminate\Database\Seeder;

class StockStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'code'  => 'in_stock',
                'color' => '#ffffff',
                'bg_color' => '#22c55e',
                'uk'    => 'В наявності',
                'en'    => 'In stock',
            ],
            [
                'code'  => 'out_of_stock',
                'color' => '#ffffff',
                'bg_color' => '#ef4444',
                'uk'    => 'Немає в наявності',
                'en'    => 'Out of stock',
            ],
            [
                'code'  => 'on_order',
                'color' => '#ffffff',
                'bg_color' => '#f59e0b',
                'uk'    => 'Під замовлення',
                'en'    => 'On order',
            ],
        ];

        foreach ($statuses as $data) {
            $status = StockStatus::create([
                'store_id'  => 1,
                'code'      => $data['code'],
                'color'     => $data['color'],
                'bg_color'  => $data['bg_color'],
                'is_active' => true,
            ]);

            StockStatusTranslation::create([
                'stock_status_id' => $status->id,
                'language_id'     => 1,
                'name'            => $data['uk'],
            ]);

            StockStatusTranslation::create([
                'stock_status_id' => $status->id,
                'language_id'     => 2,
                'name'            => $data['en'],
            ]);
        }
    }
}
