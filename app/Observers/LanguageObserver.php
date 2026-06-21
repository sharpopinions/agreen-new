<?php

namespace App\Observers;

use App\Models\Badge;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use App\Models\StockStatus;

class LanguageObserver
{
    public function created(Language $language): void
    {
        $storeId = $language->store_id;

        // Після міграції 2026_06_14_000001 slug є nullable — використовуємо null для пустого slug.
        // name — NOT NULL, але '' — валідний порожній рядок.

        Category::where('store_id', $storeId)->each(function (Category $category) use ($language) {
            $category->translations()->firstOrCreate(
                ['language_id' => $language->id],
                ['name' => '', 'slug' => null, 'description' => null]
            );
        });

        Brand::where('store_id', $storeId)->each(function (Brand $brand) use ($language) {
            $brand->translations()->firstOrCreate(
                ['language_id' => $language->id],
                ['name' => '', 'slug' => null, 'description' => null]
            );
        });

        Badge::where('store_id', $storeId)->each(function (Badge $badge) use ($language) {
            $badge->translations()->firstOrCreate(
                ['language_id' => $language->id],
                ['name' => '']
            );
        });

        Product::where('store_id', $storeId)->each(function (Product $product) use ($language) {
            $product->translations()->firstOrCreate(
                ['language_id' => $language->id],
                ['name' => '', 'slug' => null, 'description' => null]
            );
        });

        StockStatus::where('store_id', $storeId)->each(function (StockStatus $status) use ($language) {
            $status->translations()->firstOrCreate(
                ['language_id' => $language->id],
                ['name' => '']
            );
        });
    }
}
