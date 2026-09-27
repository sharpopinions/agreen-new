<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentProvider;
use App\Models\Product;
use App\Models\ShippingProvider;
use App\Models\User;
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
        'city'     => 'Київ',
        'address'  => 'Відділення №5',
    ];

    /** Дані форми зі способами доставки/оплати за драйвером (id беруться з БД). */
    private function customer(string $delivery = 'nova_poshta', string $payment = 'invoice_vat'): array
    {
        return $this->customer + [
            'delivery' => ShippingProvider::where('driver', $delivery)->value('id'),
            'payment'  => PaymentProvider::where('driver', $payment)->value('id'),
        ];
    }

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

        $this->post('/checkout', $this->customer())->assertRedirect(route('order.thanks'));

        $this->assertSame(2, Order::count());

        $regularOrder = Order::where('type', 'regular')->with('items')->firstOrFail();
        $this->assertSame('nova_poshta', $regularOrder->shippingProvider->driver);
        $this->assertSame('invoice_vat', $regularOrder->paymentProvider->driver);
        $this->assertSame('pending', $regularOrder->status->key);
        $this->assertSame(2, $regularOrder->items->first()->quantity);
        $this->assertEquals(round($regular->price * 2, 2), (float) $regularOrder->total);
        $this->assertMatchesRegularExpression('/^A-\d{4}-\d{5}$/', $regularOrder->number);

        // Передзамовлення — без оплати та доставки (ТЗ)
        $preorderOrder = Order::where('type', 'preorder')->firstOrFail();
        $this->assertNull($preorderOrder->shipping_provider_id);
        $this->assertNull($preorderOrder->payment_provider_id);
        $this->assertSame('pending', $preorderOrder->status->key);

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
        $this->post('/checkout', $this->customer())->assertRedirect(route('cart'));
        $this->assertSame(0, Order::count());
    }

    public function test_pickup_does_not_require_city(): void
    {
        $this->post('/cart', ['product_id' => $this->inStockProduct()->id]);

        $data = $this->customer('pickup', 'card_transfer');
        unset($data['city'], $data['address']);

        $this->post('/checkout', $data)->assertRedirect(route('order.thanks'));
        $this->assertSame('pickup', Order::firstOrFail()->shippingProvider->driver);
    }

    public function test_inactive_or_role_restricted_methods_are_rejected(): void
    {
        $this->post('/cart', ['product_id' => $this->inStockProduct()->id]);

        // Відстрочка — лише для бізнес-клієнтів
        $this->post('/checkout', $this->customer('nova_poshta', 'deferred'))->assertSessionHasErrors('payment');

        ShippingProvider::where('driver', 'nova_poshta')->update(['is_active' => false]);
        $this->post('/checkout', $this->customer('nova_poshta', 'invoice_vat'))->assertSessionHasErrors('delivery');

        $this->assertSame(0, Order::count());
    }

    public function test_business_client_can_use_deferred_payment(): void
    {
        $user = User::factory()->create(['role' => 'business_client']);
        $this->actingAs($user)->post('/cart', ['product_id' => $this->inStockProduct()->id]);

        $this->actingAs($user)->get('/checkout')->assertInertia(fn($page) => $page
            ->where('paymentMethods', fn($methods) => collect($methods)->contains('name', 'Відстрочка платежу')));

        $this->actingAs($user)->post('/checkout', $this->customer('nova_poshta', 'deferred'))
            ->assertRedirect(route('order.thanks'));

        $order = Order::firstOrFail();
        $this->assertSame($user->id, $order->user_id);
        $this->assertSame('deferred', $order->paymentProvider->driver);
    }
}
