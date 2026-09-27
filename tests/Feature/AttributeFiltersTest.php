<?php

namespace Tests\Feature;

use App\Filament\Resources\AttributeDefinitionResource\Pages\CreateAttributeDefinition;
use App\Filament\Resources\AttributeDefinitionResource\Pages\EditAttributeDefinition;
use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Models\AttributeDefinition;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AttributeFiltersTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true; // з AttributeSeeder: зернистість, тип системи, колір, обʼєм

    private function def(string $ukName): AttributeDefinition
    {
        return AttributeDefinition::whereHas('translations', fn($q) => $q->where('name', $ukName))->with('values.translations')->firstOrFail();
    }

    private function value(string $def, string $name): AttributeValue
    {
        return $this->def($def)->values->first(fn($v) => $v->translations->contains('name', $name));
    }

    public function test_category_page_shows_configured_filters_with_counts(): void
    {
        $this->get('/catalog/lakofarbovi-materialy')->assertInertia(fn($page) => $page
            ->has('attributeFilters', 3)
            ->where('attributeFilters.0.name', 'Тип системи')
            ->where('attributeFilters.0.display', 'checkbox')
            ->where('attributeFilters.0.values', fn($values) => collect($values)->firstWhere('name', '2K')['count'] === 2)
            ->where('attributeFilters.1.display', 'color_swatch')
            ->where('attributeFilters.1.values.0.raw', '#ffffff')
            ->where('attributeFilters.2.display', 'range')
            ->where('attributeFilters.2.min', 1)
            ->where('attributeFilters.2.max', 1));

        // Без налаштованих фільтрів — жодних груп
        $this->get('/catalog/all')->assertInertia(fn($page) => $page->has('attributeFilters', 0));
    }

    public function test_filtering_by_values_and_range(): void
    {
        $twoK = $this->value('Тип системи', '2K');
        $grey = $this->value('Колір', 'Сірий');
        $system = $this->def('Тип системи')->id;
        $color  = $this->def('Колір')->id;
        $volume = $this->def('Обʼєм, л')->id;

        $skus = fn($response) => collect($response->viewData('page')['props']['products']['data'])->pluck('sku')->sort()->values()->all();

        $this->assertSame(['NOV-LAK-001', 'VP-08'], $skus($this->get("/catalog/lakofarbovi-materialy?attr[{$system}][]={$twoK->id}")));

        // «і» між характеристиками
        $this->assertSame([], $skus($this->get("/catalog/lakofarbovi-materialy?attr[{$system}][]={$twoK->id}&attr[{$color}][]={$grey->id}")));

        // Діапазон обʼєму 0,9–2 л
        $this->assertSame(['NOV-GRUN-001', 'NOV-LAK-001'], $skus($this->get("/catalog/lakofarbovi-materialy?attr[{$volume}][min]=0.9&attr[{$volume}][max]=2")));

        // Сторінка з фільтром не індексується; чужі id ігноруються
        $this->get("/catalog/lakofarbovi-materialy?attr[{$system}][]={$twoK->id}")->assertSee('noindex, nofollow', false);
        $this->get('/catalog/lakofarbovi-materialy?attr[999][]=1')->assertInertia(fn($page) => $page->where('products.total', 3));
    }

    public function test_subcategory_inherits_parent_filters(): void
    {
        $child = Category::whereHas('parent.translations', fn($q) => $q->where('slug', 'lakofarbovi-materialy'))->with('translations')->firstOrFail();
        $slug  = $child->translations->firstWhere('language_id', 1)->slug;
        $twoK  = $this->value('Тип системи', '2K');
        $system = $this->def('Тип системи')->id;

        // Підкатегорія без власних фільтрів приймає фільтри батьківської (параметр не відкинуто)
        $this->get("/catalog/{$slug}?attr[{$system}][]={$twoK->id}")
            ->assertInertia(fn($page) => $page->where("filters.attr.{$system}.values", [$twoK->id]));
    }

    public function test_product_page_groups_attribute_values(): void
    {
        $slug = Product::where('sku', 'NOV-GRUN-001')->firstOrFail()->translations()->where('language_id', 1)->value('slug');

        $this->get("/p/{$slug}")->assertInertia(fn($page) => $page
            ->where('product.attributes', fn($attrs) => collect($attrs)->contains(fn($a) => $a['name'] === 'Колір' && $a['value'] === 'Сірий')));
    }

    public function test_admin_manages_attribute_values_product_and_category_filters(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'content']));

        Livewire::test(CreateAttributeDefinition::class)
            ->fillForm([
                'type'       => 'select',
                'uk_name'    => 'Розмір диска',
                'is_filterable' => true,
                'is_active'  => true,
                'value_rows' => [['name_uk' => '125 мм', 'name_en' => '125 mm'], ['name_uk' => '150 мм', 'name_en' => '']],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $def = $this->def('Розмір диска');
        $this->assertSame(['125 мм', '150 мм'], $def->values->map(fn($v) => $v->translations->firstWhere('language_id', 1)->name)->all());
        // Порожній переклад — береться українська назва
        $this->assertSame('150 мм', $def->values[1]->translations->firstWhere('language_id', 2)?->name);

        // Видалення значення зі списку
        $edit = Livewire::test(EditAttributeDefinition::class, ['record' => $def->id]);
        $rows = $edit->get('data.value_rows');
        array_pop($rows);
        $edit->set('data.value_rows', $rows)->call('save')->assertHasNoFormErrors();
        $this->assertSame(1, $def->values()->count());

        // Товару — значення, категорії — фільтр
        $product = Product::where('sku', 'MA-120')->firstOrFail();
        $v125 = $def->values()->first();
        $edit = Livewire::test(EditProduct::class, ['record' => $product->id]);
        $rows = $edit->get('data.attribute_rows');
        $rows[] = ['attribute_definition_id' => (string) $def->id, 'value_ids' => [(string) $v125->id]];
        $edit->set('data.attribute_rows', $rows)->call('save')->assertHasNoFormErrors();
        $this->assertTrue($product->attributeValues()->whereKey($v125->id)->exists());
        $this->assertSame(2, $product->attributeValues()->count(), 'Зернистість P120 має лишитися');

        $category = Category::whereHas('translations', fn($q) => $q->where('slug', 'abrazyvni-materialy'))->firstOrFail();
        $edit = Livewire::test(EditCategory::class, ['record' => $category->id]);
        $rows = $edit->get('data.filter_rows');
        $rows[] = ['attribute_definition_id' => (string) $def->id, 'display_type' => 'checkbox'];
        $edit->set('data.filter_rows', $rows)->call('save')->assertHasNoFormErrors();

        $this->get('/catalog/abrazyvni-materialy')->assertInertia(fn($page) => $page
            ->has('attributeFilters', 2)
            ->where('attributeFilters.1.name', 'Розмір диска')
            ->where('attributeFilters.1.values.0.count', 1));
    }
}
