<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\RelationManagers\CashbackRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\CashbackTransaction;
use App\Models\CustomerDocument;
use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ShippingProvider;
use App\Models\User;
use App\Notifications\BusinessRoleDecision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private User $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = User::factory()->create([
            'role' => 'client', 'name' => 'Іван', 'last_name' => 'Іваненко',
            'phone' => '+380671234567', 'password' => Hash::make('secret123'),
        ]);
    }

    private function order(User $user, float $total = 1000, string $status = 'pending'): Order
    {
        $product = Product::where('is_active', true)->first();
        $order = Order::create([
            'user_id' => $user->id, 'number' => 'tmp-' . uniqid(), 'type' => 'regular',
            'order_status_id' => OrderStatus::where('key', $status)->value('id'),
            'customer_name' => 'Іван', 'customer_phone' => '+380671234567', 'customer_email' => $user->email,
            'total' => $total, 'locale' => 'uk',
        ]);
        $order->update(['number' => Order::makeNumber($order->id)]);
        $order->items()->create([
            'product_id' => $product->id, 'sku' => $product->sku, 'name' => 'Товар', 'price' => $total, 'quantity' => 1, 'total' => $total,
        ]);

        return $order;
    }

    public function test_guest_is_redirected_and_sections_render(): void
    {
        $this->get('/account/orders')->assertRedirect('/login');

        $this->actingAs($this->client);
        foreach (['' => 'Catalog', '/orders' => 'Orders', '/documents' => 'Documents', '/terms' => 'Terms',
            '/study' => 'Study', '/support' => 'Support', '/personal' => 'Personal', '/settings' => 'Settings'] as $path => $page) {
            $this->get('/account' . $path)->assertOk()->assertInertia(fn(Assert $p) => $p->component('Account/' . $page));
        }
    }

    public function test_orders_list_only_own_and_repeat_fills_cart(): void
    {
        $mine  = $this->order($this->client);
        $other = $this->order(User::factory()->create(['role' => 'client']));

        $this->actingAs($this->client)->get('/account/orders')
            ->assertInertia(fn(Assert $p) => $p->has('orders.data', 1)->where('orders.data.0.number', $mine->number));

        $this->post("/account/orders/{$other->id}/repeat")->assertNotFound();
        $this->post("/account/orders/{$mine->id}/repeat")->assertRedirect('/cart');
        $this->assertSame([$mine->items->first()->product_id => 1], session('cart'));
    }

    public function test_documents_are_private_to_owner(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('customer-documents/a.pdf', '%PDF-1.4 test');
        $doc = CustomerDocument::create([
            'user_id' => $this->client->id, 'type' => 'invoice', 'title' => 'Рахунок №1', 'path' => 'customer-documents/a.pdf',
        ]);
        $this->assertGreaterThan(0, $doc->fresh()->size);

        $this->actingAs($this->client)->get('/account/documents?type=invoice')
            ->assertInertia(fn(Assert $p) => $p->has('documents', 1)->where('type', 'invoice'));
        $this->get("/account/documents/{$doc->id}")->assertOk()->assertDownload();

        $this->actingAs(User::factory()->create(['role' => 'client']))
            ->get("/account/documents/{$doc->id}")->assertNotFound();
    }

    public function test_cashback_accrues_once_when_order_completed(): void
    {
        $order = $this->order($this->client, 2000);
        $completed = OrderStatus::where('key', 'completed')->value('id');

        $order->update(['order_status_id' => $completed]);
        $order->update(['order_status_id' => OrderStatus::where('key', 'delivered')->value('id')]);
        $order->update(['order_status_id' => $completed]);

        // Базовий рівень — 1%
        $this->assertSame(1, CashbackTransaction::count());
        $this->assertEquals(20, CashbackTransaction::first()->amount);

        $this->actingAs($this->client)->get('/account/terms')->assertInertia(fn(Assert $p) => $p
            ->where('cashback.balance', 20)
            ->where('cashback.level.name', 'Базовий')
            ->where('cashback.next.name', 'Срібний')
            ->has('history', 1));
    }

    public function test_support_request_creates_lead(): void
    {
        Notification::fake();

        $this->actingAs($this->client)->post('/account/support', ['kind' => 'service', 'message' => 'Не працює диспенсер'])
            ->assertSessionHas('notice');

        $lead = Lead::where('type', 'support')->first();
        $this->assertSame($this->client->id, $lead->user_id);
        $this->assertSame('Сервіс', $lead->payload['request']);

        $this->get('/account/support')->assertInertia(fn(Assert $p) => $p->has('requests', 1)->where('requests.0.label', 'Відкритий'));

        // Публічна форма не приймає тип support
        $this->post('/forms/support', ['name' => 'X', 'phone' => '+380671112233'])->assertNotFound();
    }

    public function test_personal_data_saved_and_prefills_checkout(): void
    {
        $np = ShippingProvider::available()->first();

        $this->actingAs($this->client)->put('/account/personal', [
            'name' => 'Петро', 'last_name' => 'Петренко', 'phone' => '050 111 22 33', 'company' => 'ТОВ Тест',
            'delivery' => $np->id, 'city' => 'Львів', 'address' => 'Відділення 5',
        ])->assertSessionHasNoErrors();

        $u = $this->client->fresh();
        $this->assertSame('+380501112233', $u->phone);
        $this->assertSame($np->id, $u->default_shipping_provider_id);

        $this->post('/cart', ['product_id' => Product::where('is_active', true)->value('id')]);
        $this->get('/checkout')->assertInertia(fn(Assert $p) => $p
            ->where('prefill.name', 'Петро Петренко')
            ->where('prefill.city', 'Львів')
            ->where('prefill.delivery', $np->id));
    }

    public function test_settings_password_and_delete(): void
    {
        $this->actingAs($this->client)->put('/account/notifications', ['email_orders' => false, 'sms_orders' => true, 'newsletter' => true])
            ->assertSessionHasNoErrors();
        $this->assertFalse($this->client->fresh()->notificationSettings()['email_orders']);

        $this->put('/account/password', ['current_password' => 'wrong', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])
            ->assertSessionHasErrors('current_password');
        $this->put('/account/password', ['current_password' => 'secret123', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])
            ->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('newpass123', $this->client->fresh()->password));

        $this->delete('/account', ['password' => 'newpass123'])->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull(User::find($this->client->id));
    }

    public function test_manager_approves_business_request(): void
    {
        Notification::fake();
        $this->client->update(['requested_role' => 'business_partner', 'role_requested_at' => now(), 'company' => 'ТОВ Авто']);
        $admin = User::factory()->create(['role' => 'admin']);
        $manager = User::factory()->create(['role' => 'manager']);

        $this->actingAs($manager);
        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords([$this->client])
            ->assertCanNotSeeTableRecords([$admin])
            ->callTableAction('approve', $this->client);

        $this->assertSame('business_partner', $this->client->fresh()->role);
        $this->assertNull($this->client->fresh()->requested_role);
        Notification::assertSentTo($this->client, BusinessRoleDecision::class, fn($n) => $n->approved);

        Livewire::test(EditUser::class, ['record' => $this->client->id])->assertOk();
        $this->order($this->client);
        foreach ([OrdersRelationManager::class, DocumentsRelationManager::class, CashbackRelationManager::class] as $rm) {
            Livewire::test($rm, ['ownerRecord' => $this->client, 'pageClass' => EditUser::class])->assertOk();
        }
        Livewire::test(CashbackRelationManager::class, ['ownerRecord' => $this->client, 'pageClass' => EditUser::class])
            ->callTableAction('create', data: ['amount' => -5, 'description' => 'Списано'])
            ->assertHasNoTableActionErrors();
        $this->assertEquals(-5, $this->client->cashbackTransactions()->sum('amount'));
        $this->get('/admin/users/' . $admin->id . '/edit')->assertNotFound();
    }
}
