<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StoreSeeder::class,
            LanguageSeeder::class,
            CurrencySeeder::class,
            StockStatusSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            BadgeSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
