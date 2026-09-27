<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreScopeTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_new_records_get_current_store(): void
    {
        $category = Category::create(['sort_order' => 99]);

        $this->assertSame(1, (int) $category->store_id);
    }

    public function test_other_store_data_is_invisible(): void
    {
        $other = Store::query()->forceCreate(['name' => 'Other', 'domain' => 'other.test', 'is_active' => true]);

        $foreign = Product::query()->forceCreate([
            'store_id' => $other->id, 'sku' => 'FOREIGN-1', 'price' => 1, 'is_active' => true,
        ]);
        $foreign->translations()->create([
            'language_id' => Language::where('code', 'uk')->value('id'),
            'name' => 'Чужий товар Mirka', 'slug' => 'chuzhyi-tovar',
        ]);

        $this->assertNull(Product::where('sku', 'FOREIGN-1')->first());
        $this->assertNotNull(Product::withoutGlobalScope('store')->where('sku', 'FOREIGN-1')->first());

        $this->get('/p/chuzhyi-tovar')->assertNotFound();
        $this->getJson('/search?q=Чужий')->assertJsonCount(0, 'products');
    }
}
