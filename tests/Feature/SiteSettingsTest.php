<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings as SiteSettingsPage;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_defaults_are_shared_with_frontend(): void
    {
        $this->get('/')->assertInertia(fn($page) => $page
            ->where('site.phone.href', 'tel:+380970757170')
            ->where('site.emails.0', 'info@a-green.com.ua')
            ->where('site.address', '02660, м. Київ, вул. Крайня, 1'));
    }

    public function test_admin_edits_settings_and_site_shows_them(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(SiteSettingsPage::class)
            ->fillForm([
                'phone'            => '+38 (044) 111-22-33',
                'emails'           => ['sales@example.com'],
                'address.uk'       => 'Львів, вул. Тестова, 5',
                'schedule_short.uk' => 'Пн–Пт 8:00–17:00',
                'socials.instagram' => 'https://instagram.com/agreen',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $site = SiteSettings::forFrontend('uk');
        $this->assertSame(['label' => '+38 (044) 111-22-33', 'href' => 'tel:+380441112233'], $site['phone']);
        $this->assertSame(['sales@example.com'], $site['emails']);
        $this->assertSame('Львів, вул. Тестова, 5', $site['address']);
        $this->assertSame([['key' => 'instagram', 'url' => 'https://instagram.com/agreen']], $site['socials']);

        // Англійська не змінювалась — лишається значення за замовчуванням
        $this->assertSame('1 Krainia St., Kyiv, 02660', SiteSettings::forFrontend('en')['address']);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        Livewire::test(SiteSettingsPage::class)
            ->fillForm(['emails' => ['not-an-email']])
            ->call('save')
            ->assertHasFormErrors();
    }

    public function test_only_admin_and_content_can_open_page(): void
    {
        foreach (['admin' => 200, 'content' => 200, 'manager' => 403] as $role => $status) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get('/admin/site-settings')->assertStatus($status);
        }
    }
}
