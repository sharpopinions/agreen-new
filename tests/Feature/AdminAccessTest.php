<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function userWithRole(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_customer_cannot_access_admin(): void
    {
        foreach (User::CUSTOMER_ROLES as $role) {
            $this->actingAs($this->userWithRole($role))->get('/admin')->assertForbidden();
        }
    }

    public function test_staff_can_open_dashboard(): void
    {
        foreach (User::STAFF_ROLES as $role) {
            $this->actingAs($this->userWithRole($role))->get('/admin')->assertOk();
        }
    }

    public function test_sections_are_limited_by_role(): void
    {
        $matrix = [
            // розділ            admin  manager content
            '/admin/products'  => [200,  200,    200],
            '/admin/orders'    => [200,  200,    403],
            '/admin/users'     => [200,  403,    403],
            '/admin/languages' => [200,  403,    403],
            '/admin/order-statuses'     => [200, 403, 403],
            '/admin/shipping-providers' => [200, 403, 403],
            '/admin/payment-providers'  => [200, 403, 403],
        ];

        foreach ($matrix as $url => [$admin, $manager, $content]) {
            $this->actingAs($this->userWithRole('admin'))->get($url)->assertStatus($admin);
            $this->actingAs($this->userWithRole('manager'))->get($url)->assertStatus($manager);
            $this->actingAs($this->userWithRole('content'))->get($url)->assertStatus($content);
        }
    }

    public function test_create_admin_command(): void
    {
        $this->artisan('app:create-admin', ['email' => 'boss@example.com', '--password' => 'secret123'])
            ->assertSuccessful();

        $this->assertSame('admin', User::where('email', 'boss@example.com')->value('role'));
    }
}
