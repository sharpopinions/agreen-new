<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::create([
            'store_id'   => 1,
            'code'       => 'uk',
            'name'       => 'Українська',
            'is_default' => true,
            'is_active'  => true,
        ]);

        Language::create([
            'store_id'   => 1,
            'code'       => 'en',
            'name'       => 'English',
            'is_default' => false,
            'is_active'  => true,
        ]);
    }
}
