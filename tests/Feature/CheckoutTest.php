<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private array $customer = [
        'name'     => 'Тест Тестенко',
        'phone'    => '+380 67 123 45 67',
        'email'    => 'test@example.com',
        'delivery' => 'nova_poshta',
        'city'     => 'Київ',
        'address'  => 'Відділення №5',
        'payment'  => 'invoice_vat',
    ];

    private function inStockProduct(): Product
    {
        $product = Product::where('is_active', true)->firstOrFail();
        $product->stock_quantity = 5;
        $product->save();

        return $product;
    }

    private function preorderProduct(): Product
    {
        $product = Product::where('is_active', true)->skip(1)->firstOrFail();
        $product->stock_quantity = 0;
        $product->save();

        return $product;
    }

    public function test_quantity_is_capped_by_stock(): void
    {
        $product = $this->inStockProduct();

        $this->post('/cart', ['product_id' => $product->id, 'quantity' => 50])->assertRedirect();

        $this->assertSame([$product->id => 5], session('cart'));
    }

    public function test_regular_and_preorder_items_become_two_orders(): void
    {
        $regular  = $this->inStockProduct();
        $preorder = $this->preorderProduct();

        $this->post('/cart', ['product_id' => $regular->id, 'quantity' => 2]);
        $this->post('/cart', ['product_id' => $preorder->id, 'quantity' => 1]);

        $this->post('/checkout', $this->customer)->assertRedirect(route('order.thanks'));

        $this->assertSame(2, Order::count());

        $regularOrder = Order::where('type', 'regular')->with('items')->firstOrFail();
        $this->assertSame('nova_poshta', $regularOrder->delivery_method);
        $this->assertSame('invoice_vat', $regularOrder->payment_method);
        $this->assertSame(2, $regularOrder->items->first()->quantity);
        $this->assertEquals(round($regular->price * 2, 2), (float) $regularOrder->total);
        $this->assertMatchesRegularExpression('/^A-\d{4}-\d{5}$/', $regularOrder->number);

        // Передзамовлення — без оплати та доставки (ТЗ)
        $preorderOrder = Order::where('type', 'preorder')->firstOrFail();
        $this->assertNull($preorderOrder->delivery_method);
        $this->assertNull($preorderOrder->payment_method);

        $this->assertEmpty(session('cart'));
    }

    public function test_preorder_only_cart_does_not_require_delivery_or_payment(): void
    {
        $preorder = $this->preorderProduct();
        $this->post('/cart', ['product_id' => $preorder->id]);

        $this->post('/checkout', [
            'name'  => 'Тест',
            'phone' => '+380671234567',
            'email' => 'test@example.com',
        ])->assertRedirect(route('order.thanks'));

        $this->assertSame(1, Order::where('type', 'preorder')->count());
    }

    public function test_invalid_checkout_is_rejected_and_cart_is_kept(): void
    {
        $product = $this->inStockProduct();
        $this->post('/cart', ['product_id' => $product->id]);

        $this->post('/checkout', ['name' => '', 'phone' => 'abc', 'email' => 'x', 'delivery' => 'teleport'])
            ->assertSessionHasErrors(['name', 'phone', 'email', 'delivery', 'city', 'payment']);

        $this->assertSame(0, Order::count());
        $this->assertNotEmpty(session('cart'));
    }

    public function test_empty_cart_cannot_checkout(): void
    {
        $this->get('/checkout')->assertRedirect(route('cart'));
        $this->post('/checkout', $this->customer)->assertRedirect(route('cart'));
        $this->assertSame(0, Order::count());
    }
}
