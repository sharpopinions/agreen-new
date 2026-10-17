<?php

namespace Tests\Feature;

use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\PageResource\Pages\ListPages;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true; // PageSeeder: about, delivery, returns, public-offer, privacy

    public function test_seeded_pages_render_with_blocks(): void
    {
        $this->get('/about')->assertOk()->assertInertia(fn($page) => $page
            ->component('Page')
            ->where('page.title', 'Про компанію')
            ->where('page.blocks.1.stats.0.value', '10+')
            ->where('page.blocks.3.type', 'brands')
            ->has('page.blocks.3.brands', 8));

        $this->get('/public-offer')->assertInertia(fn($page) => $page
            ->where('page.layout', 'document')
            ->where('page.toc.0', 'Предмет договору')
            ->has('page.toc', 5));

        foreach (['/delivery', '/returns', '/privacy'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_unknown_or_hidden_page_shows_design_404(): void
    {
        $this->get('/no-such-page')->assertNotFound()->assertInertia(fn($page) => $page->component('NotFound'));
        $this->get('/a/b/c')->assertNotFound()->assertInertia(fn($page) => $page->component('NotFound'));
        $this->get('/p/no-such-product')->assertNotFound()->assertInertia(fn($page) => $page->component('NotFound'));

        Page::where('key', 'privacy')->update(['is_active' => false]);
        $this->get('/privacy')->assertNotFound();
    }

    public function test_admin_builds_page_from_blocks(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'content']));

        Livewire::test(CreatePage::class)
            ->fillForm([
                'layout'    => 'landing',
                'is_active' => true,
                'uk_title'  => 'Гарантія',
                'uk_blocks' => [
                    ['type' => 'text', 'data' => ['html' => '<p>Гарантія <strong>12 місяців</strong></p><script>x</script>']],
                    ['type' => 'cta', 'data' => ['heading' => 'Питання?', 'text' => '', 'buttons' => [['label' => 'Контакти', 'url' => '/contacts', 'style' => 'primary']]]],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        // Порожній slug — згенеровано з назви (латиницею)
        $slug = \App\Models\PageTranslation::where('title', 'Гарантія')->value('slug');
        $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', (string) $slug);
        $this->get('/' . $slug)->assertOk()->assertInertia(fn($page) => $page
            ->where('page.blocks.0.html', '<p>Гарантія <strong>12 місяців</strong></p>')
            ->where('page.blocks.1.buttons.0.url', '/contacts'));
    }

    public function test_reserved_slug_is_rejected_and_system_pages_cannot_be_deleted(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $about = Page::where('key', 'about')->firstOrFail();

        Livewire::test(EditPage::class, ['record' => $about->id])
            ->fillForm(['uk_slug' => 'catalog'])
            ->call('save')
            ->assertHasFormErrors(['uk_slug']);

        Livewire::test(ListPages::class)->assertTableActionHidden('delete', $about);
    }

    public function test_manager_has_no_access_to_pages(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'manager']))
            ->get('/admin/pages')->assertForbidden();
    }

    public function test_partners_page_with_lead_form(): void
    {
        \Illuminate\Support\Facades\Notification::fake();

        $this->get('/partners')->assertOk()->assertInertia(fn($page) => $page
            ->where('page.blocks.0.type', 'split')
            ->where('page.blocks.1.type', 'lead_form')
            ->where('page.blocks.1.form', 'partner')
            ->has('page.blocks.1.options', 3));

        $this->post('/forms/partner', [
            'company' => 'ТОВ Тест', 'name' => 'Іван', 'phone' => '+380671112233', 'email' => 'i@t.com',
            'partner_type' => 'Оптовий покупець',
        ])->assertSessionHas('leadSent', 'partner');

        $this->assertSame(['partner_type' => 'Оптовий покупець'], \App\Models\Lead::firstOrFail()->payload);
    }
}
