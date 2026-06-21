<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::create([
            'store_id'   => 1,
            'code'       => 'UAH',
            'name'       => 'Українська гривня',
            'symbol'     => '₴',
            'rate'       => 1.0000,
            'is_default' => true,
            'is_active'  => true,
        ]);

        Currency::create([
            'store_id'   => 1,
            'code'       => 'USD',
            'name'       => 'Долар США',
            'symbol'     => '$',
            'rate'       => 0.0244,
            'is_default' => false,
            'is_active'  => true,
        ]);
    }
}
