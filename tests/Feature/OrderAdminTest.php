<?php

namespace Tests\Feature;

use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\PaymentProviderResource\Pages\EditPaymentProvider;
use App\Filament\Resources\ShippingProviderResource\Pages\CreateShippingProvider;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PaymentProvider;
use App\Models\ShippingProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderAdminTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    private function order(): Order
    {
        return Order::create([
            'number' => 'A-2026-00001', 'type' => 'regular',
            'order_status_id' => OrderStatus::initialId(),
            'customer_name' => 'Тест', 'customer_phone' => '+380671234567', 'customer_email' => 't@example.com',
            'shipping_provider_id' => ShippingProvider::where('driver', 'nova_poshta')->value('id'),
            'payment_provider_id'  => PaymentProvider::where('driver', 'invoice_vat')->value('id'),
            'total' => 100, 'locale' => 'uk',
        ]);
    }

    public function test_order_pages_render_with_provider_names(): void
    {
        $order = $this->order();

        $this->get('/admin/orders')->assertOk()->assertSee('A-2026-00001')->assertSee('Нове');
        $this->get("/admin/orders/{$order->id}")->assertOk()->assertSee('Нова Пошта')->assertSee('Рахунок-фактура з ПДВ');
    }

    public function test_manager_processes_order(): void
    {
        $order   = $this->order();
        $shipped = OrderStatus::where('key', 'shipped')->value('id');

        Livewire::test(EditOrder::class, ['record' => $order->id])
            ->fillForm(['order_status_id' => $shipped, 'payment_status' => 'paid', 'tracking_number' => '20450000000000'])
            ->call('save')
            ->assertHasNoFormErrors();

        $order->refresh();
        $this->assertSame('shipped', $order->status->key);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('20450000000000', $order->tracking_number);
    }

    public function test_admin_creates_shipping_provider_with_translations(): void
    {
        Livewire::test(CreateShippingProvider::class)
            ->fillForm(['driver' => 'dhl', 'uk_name' => 'DHL', 'uk_description' => '2–5 днів', 'settings.requires_address' => true, 'is_active' => true])
            ->call('create')
            ->assertHasNoFormErrors();

        $provider = ShippingProvider::where('driver', 'dhl')->with('translations')->firstOrFail();
        $this->assertSame('DHL', $provider->name);
        $this->assertSame('2–5 днів', $provider->description);
        $this->assertTrue($provider->requiresAddress());

        $this->assertTrue(ShippingProvider::available()->contains('driver', 'dhl'));
    }

    public function test_payment_roles_are_saved(): void
    {
        $provider = PaymentProvider::where('driver', 'online')->firstOrFail();

        Livewire::test(EditPaymentProvider::class, ['record' => $provider->id])
            ->fillForm(['settings.roles' => ['business_partner']])
            ->call('save')
            ->assertHasNoFormErrors();

        $provider->refresh();
        $this->assertSame(['business_partner'], $provider->settings['roles']);
        $this->assertFalse($provider->isAvailableFor(null));
        $this->assertTrue($provider->isAvailableFor(User::factory()->make(['role' => 'business_partner'])));
    }
}
