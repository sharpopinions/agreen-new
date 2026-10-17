<?php

namespace Tests\Feature;

use App\Filament\Resources\LeadResource\Pages\EditLead;
use App\Filament\Resources\LeadResource\Pages\ListLeads;
use App\Models\Lead;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_contacts_page_shows_site_settings(): void
    {
        $this->get('/contacts')->assertOk()->assertInertia(fn($page) => $page
            ->component('Contacts')
            ->where('site.emails.0', 'info@a-green.com.ua'));
    }

    public function test_contact_form_creates_lead_and_emails_manager(): void
    {
        $this->from('/contacts')->post('/forms/contact', [
            'name' => 'Олена', 'phone' => '+380 67 111 22 33', 'message' => 'Потрібна консультація',
        ])->assertRedirect('/contacts')->assertSessionHas('leadSent', 'contact');

        $lead = Lead::firstOrFail();
        $this->assertSame('contact', $lead->type);
        $this->assertSame('new', $lead->status);
        $this->assertSame('Потрібна консультація', $lead->message);

        Notification::assertSentTo(new AnonymousNotifiable, NewLead::class,
            fn($n, $channels, $notifiable) => $notifiable->routes['mail'] === 'info@a-green.com.ua' && $n->lead->is($lead));
    }

    public function test_quick_order_and_stock_notify_need_product(): void
    {
        $product = Product::where('is_active', true)->firstOrFail();

        $this->post('/forms/quick_order', ['name' => 'Іван', 'phone' => '+380671112233', 'product_id' => $product->id, 'quantity' => 3])
            ->assertSessionHas('leadSent', 'quick_order');
        $this->assertSame(['quantity' => 3], Lead::where('type', 'quick_order')->firstOrFail()->payload);

        // Для повідомлення про надходження вистачає email
        $this->post('/forms/stock_notify', ['email' => 'buyer@example.com', 'product_id' => $product->id])
            ->assertSessionHasNoErrors();
        $this->post('/forms/stock_notify', ['product_id' => $product->id])->assertSessionHasErrors(['phone', 'email']);

        $this->post('/forms/quick_order', ['name' => 'Іван', 'phone' => '+380671112233', 'product_id' => 999999])
            ->assertSessionHasErrors('product_id');
    }

    public function test_validation_honeypot_and_unknown_form(): void
    {
        $this->post('/forms/partner', ['name' => 'Без компанії'])->assertSessionHasErrors(['company', 'phone', 'email']);
        $this->post('/forms/contact', ['name' => 'Bot', 'phone' => '+380671112233', 'website' => 'http://spam'])
            ->assertSessionHas('leadSent');
        $this->post('/forms/hack', [])->assertNotFound();

        $this->assertSame(0, Lead::count());
        Notification::assertNothingSent();
    }

    public function test_manager_processes_lead(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $lead = Lead::create(['type' => 'contact', 'name' => 'Олена', 'phone' => '+380671112233']);
        $this->actingAs($manager);

        Livewire::test(ListLeads::class)
            ->assertCanSeeTableRecords([$lead])
            ->callTableAction('take', $lead);
        $this->assertSame('in_progress', $lead->refresh()->status);
        $this->assertSame($manager->id, $lead->manager_id);

        Livewire::test(EditLead::class, ['record' => $lead->id])
            ->fillForm(['status' => 'done', 'manager_note' => 'Передзвонили, виставили рахунок'])
            ->call('save')
            ->assertHasNoFormErrors();

        $lead->refresh();
        $this->assertSame('done', $lead->status);
        $this->assertNotNull($lead->processed_at);

        $this->actingAs(User::factory()->create(['role' => 'content']))->get('/admin/leads')->assertForbidden();
    }
}
