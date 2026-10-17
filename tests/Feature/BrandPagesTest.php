<?php

namespace Tests\Feature;

use App\Filament\Resources\BrandResource\Pages\EditBrand;
use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BrandPagesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function brand(string $slug): Brand
    {
        return Brand::whereHas('translations', fn($q) => $q->where('slug', $slug))->firstOrFail();
    }

    public function test_brands_list_with_category_tabs(): void
    {
        $this->get('/brands')->assertOk()->assertInertia(fn($page) => $page
            ->component('Brands')
            ->has('brands', 8)
            ->where('brands', fn($brands) => collect($brands)->firstWhere('slug', 'novol')['count'] === 3)
            ->where('categories', fn($cats) => collect($cats)->contains('name', 'Лакофарбові матеріали')));
    }

    public function test_brand_products_tab_lists_only_brand_products_with_filters(): void
    {
        $this->get('/brands/novol')->assertOk()->assertInertia(fn($page) => $page
            ->component('Brand')
            ->where('tab', 'products')
            ->where('brand.name', 'Novol')
            ->where('products.total', 3)
            ->where('products.data', fn($items) => collect($items)->every(fn($p) => $p['brand'] === 'Novol'))
            ->where('categories', fn($cats) => collect($cats)->pluck('name')->all() === ['Лакофарбові матеріали'])
            ->where('brands', []));

        // Фільтр за кореневою категорією враховує товари підкатегорій
        $root = collect($this->get('/brands/novol')->viewData('page')['props']['categories'])->first();
        $this->get('/brands/novol?category[]=' . $root['id'])->assertInertia(fn($page) => $page->where('products.total', 3));

        $this->get('/brands/no-such-brand')->assertNotFound();
    }

    public function test_about_and_media_tabs(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'content']));
        Storage::fake('public');
        $brand = $this->brand('mirka');

        $edit = Livewire::test(EditBrand::class, ['record' => $brand->id]);
        $edit->set('data.media', [
            'v' => ['type' => 'video', 'title' => 'Огляд', 'youtube' => 'https://youtu.be/dQw4w9WgXcQ'],
            'd' => ['type' => 'pdf', 'title' => 'Каталог 2026', 'file' => []],
        ]);
        $edit->set('data.media.d.file', UploadedFile::fake()->create('catalog.pdf', 300, 'application/pdf'));
        $edit->fillForm([
            'uk_short_description' => 'Фінський виробник абразивів',
            'uk_stats'             => [['value' => '1943', 'label' => 'рік заснування']],
        ])->call('save')->assertHasNoFormErrors();

        $this->get('/brands/mirka')->assertInertia(fn($page) => $page
            ->where('brand.shortDescription', 'Фінський виробник абразивів')
            ->where('brand.stats.0.value', '1943')
            ->where('brand.hasMedia', true));

        $this->get('/brands/mirka/about')->assertOk()->assertInertia(fn($page) => $page
            ->where('tab', 'about')
            ->whereType('about', 'string')
            ->where('otherBrands', fn($others) => ! collect($others)->contains('slug', 'mirka')));

        $this->get('/brands/mirka/media')->assertOk()->assertInertia(fn($page) => $page
            ->where('media.videos.0.id', 'dQw4w9WgXcQ')
            ->where('media.documents.0.title', 'Каталог 2026')
            ->where('media.documents.0.ext', 'PDF'));

        // Без медіа — вкладки немає
        $this->get('/brands/dupont/media')->assertNotFound();
    }

    public function test_brand_with_products_cannot_be_deleted_and_hidden_brand_404(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $novol = $this->brand('novol');

        Livewire::test(\App\Filament\Resources\BrandResource\Pages\ListBrands::class)
            ->assertTableActionHidden('delete', $novol);

        $novol->update(['is_active' => false]);
        $this->get('/brands/novol')->assertNotFound();
        $this->assertGreaterThan(0, Product::where('brand_id', $novol->id)->count());
    }
}
