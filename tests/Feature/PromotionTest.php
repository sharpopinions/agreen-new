<?php

namespace Tests\Feature;

use App\Filament\Resources\PromotionResource\Pages\CreatePromotion;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true; // PromotionSeeder: 6 акцій

    private function slug(string $titleStart): string
    {
        return Promotion::whereHas('translations', fn($q) => $q->where('title', 'like', $titleStart . '%'))
            ->firstOrFail()->translations()->value('slug');
    }

    public function test_list_shows_running_promotions_only(): void
    {
        [$ended, $scheduled] = Promotion::orderBy('id')->take(2)->get();
        $ended->update(['ends_at' => now()->subDay()]);
        $scheduled->update(['starts_at' => now()->addWeek()]);

        $this->get('/sale')->assertOk()->assertInertia(fn($page) => $page
            ->component('Promotions')
            ->has('promotions', 4)
            ->where('promotions', fn($list) => ! collect($list)->pluck('id')->intersect([$ended->id, $scheduled->id])->count()));
    }

    public function test_promotion_page_lists_brand_and_category_products(): void
    {
        // Mirka + абразивні → обидва товари Mirka з категорії
        $this->get('/sale/' . $this->slug('−15%'))->assertOk()->assertInertia(fn($page) => $page
            ->component('Promotion')
            ->where('promotion.brand.name', 'Mirka')
            ->where('promotion.productsCount', 2)
            ->where('products.total', 2));

        // Лише бренд 3M → усі товари 3M
        $count3m = Product::whereHas('brand.translations', fn($q) => $q->where('slug', '3m'))->where('is_active', true)->count();
        $this->get('/sale/' . $this->slug('Безкоштовна'))->assertInertia(fn($page) => $page->where('products.total', $count3m));
    }

    public function test_manually_selected_products_take_priority_and_expired_is_404(): void
    {
        $promo   = Promotion::whereHas('translations', fn($q) => $q->where('title', 'like', '−15%%'))->firstOrFail();
        $product = Product::where('sku', 'MIR-SAND-080')->firstOrFail();
        $promo->products()->attach($product);

        $slug = $promo->translations()->value('slug');
        $this->get('/sale/' . $slug)->assertInertia(fn($page) => $page
            ->where('products.total', 1)
            ->where('products.data.0.sku', 'MIR-SAND-080'));

        $promo->update(['ends_at' => now()->subDay()]);
        $this->get('/sale/' . $slug)->assertNotFound();
    }

    public function test_admin_creates_promotion(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'content']));

        Livewire::test(CreatePromotion::class)
            ->fillForm([
                'discount_percent' => 30,
                'ends_at'          => now()->addMonth()->toDateString(),
                'is_active'        => true,
                'uk_title'         => 'Чорна пʼятниця',
                'uk_description'   => '<p>Знижки на все</p>',
                'products'         => [Product::where('sku', 'VP-08')->value('id')],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $slug = Promotion::latest('id')->firstOrFail()->translations()->value('slug');
        $this->get('/sale/' . $slug)->assertInertia(fn($page) => $page
            ->where('promotion.discount', 30)
            ->where('products.total', 1));
    }
}
