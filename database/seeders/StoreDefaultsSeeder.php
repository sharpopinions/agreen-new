<?php

namespace Database\Seeders;

use App\Support\StoreDefaults;
use Illuminate\Database\Seeder;

class StoreDefaultsSeeder extends Seeder
{
    /** Статуси замовлень, доставка й оплата для магазину A-green. */
    public function run(): void
    {
        StoreDefaults::install(1);
    }
}
