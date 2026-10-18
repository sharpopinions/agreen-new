<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductListsTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function products(int $count = 2)
    {
        return Product::where('is_active', true)->orderBy('id')->limit($count)->get();
    }

    public function test_guest_wishlist_toggle_page_preview_and_cart(): void
    {
        [$a, $b] = $this->products();

        $this->post("/wishlist/{$a->id}")->assertRedirect();
        $this->post("/wishlist/{$b->id}");
        $this->get('/')->assertInertia(fn(Assert $p) => $p->where('lists.wishlist', [$b->id, $a->id]));

        $this->get('/wishlist')->assertInertia(fn(Assert $p) => $p->component('Wishlist')->has('products', 2));
        $this->getJson('/wishlist/preview')->assertOk()->assertJsonPath('count', 2)->assertJsonCount(2, 'items');

        $this->post("/wishlist/{$a->id}"); // повторне натискання прибирає
        $this->get('/wishlist')->assertInertia(fn(Assert $p) => $p->has('products', 1));

        $this->post('/wishlist/to-cart')->assertRedirect('/cart');
        $this->assertArrayHasKey($b->id, session('cart'));

        $this->delete('/wishlist');
        $this->get('/wishlist')->assertInertia(fn(Assert $p) => $p->has('products', 0));
    }

    public function test_guest_lists_move_to_profile_after_login(): void
    {
        [$a] = $this->products(1);
        $user = User::factory()->create(['role' => 'client', 'password' => Hash::make('secret123')]);

        $this->post("/wishlist/{$a->id}");
        $this->post("/compare/{$a->id}");
        $this->post('/auth/login', ['login' => $user->email, 'password' => 'secret123']);

        $this->assertDatabaseHas('product_list_items', ['user_id' => $user->id, 'product_id' => $a->id, 'list' => 'wishlist']);
        $this->assertDatabaseHas('product_list_items', ['user_id' => $user->id, 'product_id' => $a->id, 'list' => 'compare']);

        // Ще одне додавання вже йде в профіль, без дублів
        $this->post("/wishlist/{$a->id}");
        $this->assertDatabaseMissing('product_list_items', ['user_id' => $user->id, 'list' => 'wishlist']);
        $this->post("/wishlist/{$a->id}");
        $this->assertDatabaseCount('product_list_items', 2);
    }

    public function test_compare_groups_by_category_limits_and_shows_attributes(): void
    {
        $category = Category::whereHas('translations', fn($q) => $q->where('slug', 'lakofarbovi-materialy'))->first();
        $paints   = $category->products()->where('is_active', true)->get();
        $this->assertGreaterThanOrEqual(2, $paints->count());

        foreach ($paints->take(2) as $p) {
            $this->post("/compare/{$p->id}")->assertSessionHas('notice');
        }
        $other = Product::where('is_active', true)->whereDoesntHave('categories', fn($q) => $q->where('categories.id', $category->id))->first();
        $this->post("/compare/{$other->id}");

        $this->get('/compare?category=' . $category->id)->assertInertia(fn(Assert $p) => $p
            ->component('Compare')
            ->has('tabs', 2)
            ->where('category', $category->id)
            ->has('products', 2)
            ->where('rows.0.name', 'Бренд')
            ->where('rows', fn($rows) => collect($rows)->contains('name', 'Тип системи')));

        // Ліміт 4 товари однієї категорії
        $this->delete('/compare');
        $ids = Product::where('is_active', true)->whereHas('categories', fn($q) => $q->where('categories.id', $other->categories->first()->id))->limit(5)->pluck('id');
        if ($ids->count() === 5) {
            foreach ($ids->take(4) as $id) {
                $this->post("/compare/{$id}");
            }
            $this->post("/compare/{$ids[4]}")->assertSessionHas('notice', fn($n) => str_contains($n, 'до 4'));
            $this->get('/')->assertInertia(fn(Assert $p) => $p->has('lists.compare', 4));
        }
    }

    public function test_empty_pages_render(): void
    {
        $this->get('/wishlist')->assertOk()->assertInertia(fn(Assert $p) => $p->component('Wishlist')->has('products', 0));
        $this->get('/compare')->assertOk()->assertInertia(fn(Assert $p) => $p->component('Compare')->has('products', 0));
    }
}
