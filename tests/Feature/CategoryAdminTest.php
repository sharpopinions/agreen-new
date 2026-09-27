<?php

namespace Tests\Feature;

use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryAdminTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'content']));
    }

    private function rootWithProducts(): Category
    {
        return Category::whereNull('parent_id')->whereHas('products')->with('translations')->firstOrFail();
    }

    public function test_texts_and_seo_are_shown_on_category_page(): void
    {
        $category = $this->rootWithProducts();

        Livewire::test(EditCategory::class, ['record' => $category->id])
            ->fillForm([
                'uk_short_description' => 'Круги, папір і губки для шліфування',
                'uk_description'       => '<h2>Як обрати абразив</h2><p>Текст</p>',
                'uk_meta_title'        => 'Абразиви купити в Києві',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $slug = $category->translations->firstWhere('language_id', 1)->slug;

        $this->get("/catalog/{$slug}")
            ->assertSee('<title inertia>Абразиви купити в Києві</title>', false)
            ->assertDontSee('name="robots"', false)
            ->assertInertia(fn($page) => $page
                ->where('category.shortDescription', 'Круги, папір і губки для шліфування')
                ->where('category.description', '<h2>Як обрати абразив</h2><p>Текст</p>'));

        // Сторінка з фільтром: SEO-текст не дублюємо, індексацію закриваємо
        $this->get("/catalog/{$slug}?sale=1")
            ->assertSee('content="noindex, nofollow"', false)
            ->assertInertia(fn($page) => $page->where('category.description', null));
    }

    public function test_category_cannot_become_its_own_descendant(): void
    {
        $parent = Category::whereHas('children')->firstOrFail();
        $child  = $parent->children()->first();

        Livewire::test(EditCategory::class, ['record' => $parent->id])
            ->assertFormFieldExists('parent_id', fn($field) => ! array_key_exists($parent->id, $field->getOptions())
                && ! array_key_exists($child->id, $field->getOptions()));
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        Livewire::test(ListCategories::class)
            ->assertTableActionHidden('delete', $this->rootWithProducts());
    }
}
