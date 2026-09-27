<?php

namespace Tests\Feature;

use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SlugTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    private function uk(): Language { return Language::where('code', 'uk')->firstOrFail(); }
    private function en(): Language { return Language::where('code', 'en')->firstOrFail(); }

    public function test_duplicate_slug_in_same_language_is_rejected(): void
    {
        $taken = ProductTranslation::where('language_id', $this->uk()->id)->value('slug');

        Livewire::test(CreateProduct::class)
            ->fillForm(['sku' => 'NEW-1', 'price' => 10, 'uk_name' => 'Новий', 'uk_slug' => $taken])
            ->call('create')
            ->assertHasFormErrors(['uk_slug']);
    }

    public function test_empty_slug_is_generated_from_name_and_other_language_stays_null(): void
    {
        Livewire::test(CreateProduct::class)
            ->fillForm(['sku' => 'NEW-2', 'price' => 10, 'uk_name' => 'Лак акриловий Новий', 'uk_slug' => ''])
            ->call('create')
            ->assertHasNoFormErrors();

        $product = Product::where('sku', 'NEW-2')->firstOrFail();

        $this->assertSame('lak-akrilovii-novii', $product->translations()->where('language_id', $this->uk()->id)->value('slug'));
        $this->assertNull($product->translations()->where('language_id', $this->en()->id)->value('slug'));
    }

    public function test_generated_slug_gets_suffix_when_taken(): void
    {
        foreach (['NEW-3', 'NEW-4'] as $sku) {
            Livewire::test(CreateProduct::class)
                ->fillForm(['sku' => $sku, 'price' => 10, 'uk_name' => 'Однакова назва'])
                ->call('create')
                ->assertHasNoFormErrors();
        }

        $slugs = ProductTranslation::where('language_id', $this->uk()->id)
            ->whereIn('product_id', Product::whereIn('sku', ['NEW-3', 'NEW-4'])->pluck('id'))
            ->pluck('slug')->sort()->values()->all();

        $this->assertSame(['odnakova-nazva', 'odnakova-nazva-2'], $slugs);
    }

    public function test_category_is_resolved_by_slug_of_current_language_only(): void
    {
        $category = \App\Models\Category::whereNull('parent_id')->with('translations')->firstOrFail();
        $enSlug   = $category->translations->firstWhere('language_id', $this->en()->id)?->slug;
        $ukSlug   = $category->translations->firstWhere('language_id', $this->uk()->id)?->slug;

        app()->setLocale('uk');
        $this->get("/catalog/{$ukSlug}")->assertOk();

        if ($enSlug && $enSlug !== $ukSlug) {
            $this->get("/catalog/{$enSlug}")->assertNotFound();
        }
    }
}
