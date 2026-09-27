<?php

namespace Tests\Feature;

use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductAdminTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'content']));
    }

    private function product(): Product
    {
        return Product::where('is_active', true)->with('translations')->firstOrFail();
    }

    public function test_photos_videos_and_seo_are_saved_and_shown_on_site(): void
    {
        $product = $this->product();

        $component = Livewire::test(EditProduct::class, ['record' => $product->id]);

        // Новий рядок репітера фото й відео
        $photos = $component->get('data.images');
        $photos['new-1'] = ['path' => [], 'alt' => 'Вигляд спереду'];
        $component->set('data.images', $photos);
        $component->set('data.images.new-1.path', UploadedFile::fake()->image('front.jpg', 800, 600));
        $component->set('data.videos', ['v1' => ['youtube_id' => 'https://youtu.be/dQw4w9WgXcQ']]);

        $component
            ->fillForm([
                'stock_quantity'      => 7,
                'uk_description'      => '<p>Опис <strong>жирним</strong></p><script>alert(1)</script>',
                'uk_meta_title'       => 'Купити поліроль у Києві',
                'uk_meta_description' => 'Опис для Google',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $product->refresh()->load('images', 'videos', 'translations');
        $this->assertSame(7, $product->stock_quantity);
        $this->assertCount(1, $product->images);
        $this->assertSame('Вигляд спереду', $product->images->first()->alt);
        Storage::disk('public')->assertExists($product->images->first()->path);
        $this->assertSame('dQw4w9WgXcQ', $product->videos->first()->youtube_id);

        $slug = $product->translations->firstWhere('language_id', 1)->slug;
        $this->get("/p/{$slug}")
            ->assertSee('<title inertia>Купити поліроль у Києві</title>', false)
            ->assertSee('content="Опис для Google"', false)
            ->assertInertia(fn($page) => $page
                ->where('product.description', '<p>Опис <strong>жирним</strong></p>')
                ->where('product.videos.0', 'dQw4w9WgXcQ')
                ->where('product.images.0.alt', 'Вигляд спереду'));

        $this->get('/catalog/all')->assertInertia(fn($page) => $page
            ->where('products.data', fn($items) => collect($items)->firstWhere('id', $product->id)['image'] !== null));
    }

    public function test_invalid_youtube_link_is_rejected(): void
    {
        Livewire::test(EditProduct::class, ['record' => $this->product()->id])
            ->set('data.videos', ['v1' => ['youtube_id' => 'https://vimeo.com/123']])
            ->call('save')
            ->assertHasFormErrors();
    }

    public function test_list_filters_and_bulk_hide(): void
    {
        $sale = Product::whereNotNull('old_price')->count();

        Livewire::test(ListProducts::class)
            ->filterTable('sale')
            ->assertCountTableRecords($sale);

        $ids = Product::limit(2)->pluck('id');
        Livewire::test(ListProducts::class)
            ->callTableBulkAction('hide', $ids);

        $this->assertSame(0, Product::whereIn('id', $ids)->where('is_active', true)->count());
    }

    public function test_hidden_product_is_not_on_site(): void
    {
        $product = $this->product();
        $slug = $product->translations->firstWhere('language_id', 1)->slug;
        $product->update(['is_active' => false]);

        $this->get("/p/{$slug}")->assertNotFound();
    }

    public function test_plain_text_description_becomes_paragraphs(): void
    {
        $this->assertSame("<p>Рядок 1<br>\nрядок 2</p><p>Абзац &amp; 2</p>", \App\Support\Html::clean("Рядок 1\nрядок 2\n\nАбзац & 2"));
    }
}
